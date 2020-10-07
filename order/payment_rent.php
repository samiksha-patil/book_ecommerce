<?php

session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

require_once "../connection.php";

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
Order Summary
<?php

$book_id = $_GET['id'];

$query = $link->query("SELECT * FROM book_for_rent WHERE book_id = $book_id");
    if($query->num_rows == 1){
        $row = $query->fetch_assoc();

       
        $total = $row["monthly_rate"];
        echo "$total";
       
        
    }


$user_id= $_SESSION["user_id"];

   ?>
   <?php
   if(isset($_POST["id"]) && !empty($_POST["id"]) && ($_SERVER["REQUEST_METHOD"] == "POST")){
       $id=$_SESSION["user_id"];;
       $address = $_POST['address'];
       $zip = $_POST['zip'];
       $state =$_POST['state'];
       $mode = $_POST['mode'];
       $no_months = $_POST['no_months'];
       $book_id = $_POST['id'];

       
$query = $link->query("SELECT * FROM book_for_rent WHERE book_id = $book_id");
if($query->num_rows == 1){
    $row = $query->fetch_assoc();
    $total = $row["monthly_rate"];
       
}
     
       $sql = "INSERT INTO order_item (user_id,street,zipcode,state) VALUES ('$user_id','$address','$zip', '$state')";
      
       if(mysqli_query($link, $sql))
       {

        $query1 = $link->query("SELECT ADDDATE(CURDATE(),INTERVAL $no_months MONTH ) AS date_return ");
        if($query->num_rows == 1){
        $row = $query1->fetch_assoc();
        $date_of_return = $row["date_return"];
        }
        $sql2 = "INSERT INTO queue (user_id, book_id, status, date_of_request , date_granted, date_of_return) VALUES ('$user_id','$book_id','Currently renting',NOW(),NOW(),'$date_of_return')";    
        if(mysqli_query($link, $sql2))
        {            
                      
           $sql1 = "INSERT INTO payment(order_id,payment_date,payment_amount,mode_of_payment) VALUES (LAST_INSERT_ID(),NOW(),'$total','$mode')";
           if(mysqli_query($link, $sql1))
           {
       
               
               $sql3= "UPDATE cart_item INNER JOIN book_for_rent on cart_item.book_id=book_for_rent.book_id SET cart_item.is_ordered=1, cart_item.order_id=LAST_INSERT_ID()   WHERE cart_item.user_id=$user_id AND cart_item.is_ordered=0 AND cart_item.book_id NOT IN ( SELECT book_id FROM cart_item WHERE is_ordered=1)";
               if(mysqli_query($link, $sql3))
               {
                $sql3= "UPDATE book_for_rent  SET is_available=0 WHERE book_id=$book_id";
                if(mysqli_query($link, $sql3))
                {
                header("location: ../books/user_books.php");
               echo "Payment Successful!..you will soon receive your book.";
                }
               }
           
           
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
       

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label>No of months:</label>
    <input type="number" name="no_months" class="form-control" required>

    <label>Address:</label>
    <input type="text" name="address" class="form-control" required>
    <label>Postcode / Zip:</label>
    <input type="text" name="zip" class="form-control" required>
    <label>State:</label>
    <input type="text" name="state" class="form-control"required>

    <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
<h2>Payment method</h2>

<label>Net Banking</label><input type="radio" name="mode" value="Net Banking" id="net_banking" required> 
<label>Cash on Delivery</label><input type="radio" name="mode" value="Cash on Delivery" id="Cash on Delivery" required>
<label>Credit Card</label><input type="radio" name="mode" value="Credit Card" id="Credit Card" required> 

<input type="submit" name="submit" value="Proceed to pay">        
</form>

</body>
</html>