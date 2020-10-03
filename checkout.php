<?php

session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "connection.php";
if($_SERVER["REQUEST_METHOD"] == "POST"){
$id=$_SESSION["user_id"];;
$address = $_POST['address'];
$zip = $_POST['zip'];
$state =$_POST['state'];
$mode = $_POST['mode'];

$sql = "INSERT INTO order_item (user_id,street,zipcode,state) VALUES ('$id','$address','$zip', '$state')";

if(mysqli_query($link, $sql))
{
    $sql2 = "SELECT SUM(price) FROM book NATURAL JOIN book_for_sale INNER JOIN cart_item on cart_item.book_id= book_for_sale.book_id WHERE cart_item.user_id=$id AND cart_item.is_ordered=0";
               if($result = mysqli_query($link, $sql2)){
                    $row = mysqli_fetch_assoc($result);
                    $total = $row["SUM(price)"];
               }
              
               
    $sql1 = "INSERT INTO payment(order_id,payment_date,payment_amount,mode_of_payment) VALUES (LAST_INSERT_ID(),NOW(),'$total','$mode')";
    if(mysqli_query($link, $sql1))
    {

        
        $sql3= "UPDATE cart_item INNER JOIN book_for_sale on cart_item.book_id=book_for_sale.book_id SET cart_item.is_ordered=1, cart_item.order_id=LAST_INSERT_ID()   WHERE cart_item.user_id=$id AND cart_item.is_ordered=0";
        if(mysqli_query($link, $sql3))
        {
        echo "Payment Successful!..you will soon receive your book.";
        }
    
    
    }
    } 
    
 

else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// Close connection
mysqli_close($link);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
   
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    
    <label>Address:</label>
    <input type="text" name="address" class="form-control" required>
    <label>Postcode / Zip:</label>
    <input type="text" name="zip" class="form-control" required>
    <label>State:</label>
    <input type="text" name="state" class="form-control"required>


<h2>Payment method</h2>

<label>Net Banking</label><input type="radio" name="mode" value="Net Banking" id="net_banking" required> 
<label>Cash on Delivery</label><input type="radio" name="mode" value="Cash on Delivery" id="Cash on Delivery" required>
<label>Credit Card</label><input type="radio" name="mode" value="Credit Card" id="Credit Card" required> 

<input type="submit" name="submit" value="Proceed to pay">        
</form>

</body>
</html>