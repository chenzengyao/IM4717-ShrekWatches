<?php // function for signup.html
include "dbconnect.php";
session_start();

$userid = $_SESSION['valid_user'];
$customer_id = $_SESSION['customer_id'];
$productsarray = $_SESSION['productsarray'];

//checking of forms
if (isset($_POST['submit'])) {
  if (empty($_POST['name']) || empty ($_POST['email']) || empty ($_POST['tel']) || empty ($_POST['address'])
  || empty ($_POST['payment_type']) || empty ($_POST['card_number']) || empty ($_POST['card_expiry']) || empty ($_POST['card_cvv'])) {
    echo "Please fill in the missing shipping information.";
    exit;
  }
}

$name = $_POST['name'];
$email = $_POST['email'];
$tel = $_POST['tel'];
$address = $_POST['address'];
$payment_type = $_POST['payment_type'];
$card_number = $_POST['card_number'];
$card_expiry = $_POST['card_expiry'];
$card_cvv = $_POST['card_cvv'];

//insert into shippinginfo database table
$query1 = "INSERT INTO shippinginfo (customer_id, name, email, tel, address, payment_type, card_number, card_expiry, card_cvv) VALUES ('".$customer_id."', '".$name."', '".$email."', '".$tel."', '".$address."'
  , '".$payment_type."', '".$card_number."', '".$card_expiry."', '".$card_cvv."')";
$result = $dbcnx->query($query1);

//Insert into puchaseditems database - To be used for tracking order history of users
for ($x=0;$x<count($productsarray);$x++){
  $product_id=$productsarray[$x];
  $query3 = 'select * from products '
             ."where product_id='$product_id' ";

  $result = $dbcnx->query($query3);
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $brand = $row["product_brand"];
      $name = $row["product_name"];
      $amount = $row["price"];
    }
  }

  $date = date('Y/m/d');
  $query4 = "INSERT INTO purchaseditems (customer_id, product_id, date, product_brand, product_name, price) VALUES ('".$customer_id."', '".$product_id."', '".$date."', '".$brand."', '".$name."', '".$amount."')";
  $result = $dbcnx->query($query4);
}

//Delete from shoppingcart database after payment made
$query5 = 'DELETE FROM shoppingcart' . " WHERE customer_id = '$customer_id'";
$result = $dbcnx->query($query5);

if (!$result)
	echo "Your query failed.";
else
  echo "<script>alert('Thank you for your purchase $userid! Payment has been made successfully.\\nWe will email you at: f32ee@localhost to update you on your order status!');";
  echo 'window.location.href = "index.php";';
  echo '</script>';
  //Send email of successful purchase
  $to = 'f32ee@localhost';
  $subject ='Shrek Watches Order';
  $message ='Thank you for your purchase! We will be emailing you again once we have shipped the items.';
  $headers ='From: f32ee@localhost'. "\r\n" .
            'Reply-To: f32ee@localhost'. "\r\n" .
            'X-Mailer: PHP/'. phpversion();

  mail($to, $subject, $message, $headers,'-ff32ee@localhost');

$dbcnx->close();
?>
