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
    <title>Checkout</title>
        
<link rel="stylesheet" href="../static/css/main.css" />
<link rel="stylesheet" href="../static/css/styles.css" />
<link rel="stylesheet" href="../static/css/form.css" />
<link rel="stylesheet" href="../static/css/cart.css" />
<link rel="stylesheet" href="../static/css/checkout.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1" />
   
</head>
<body>

<div class="container-top">
  <img
    class="img-top"
    src="../static/images/title-img.jpg"
    alt="Cinque Terre"
    width="1000"
    height="300"
  />
  <div class="center product-title">
    Checkout
    <div class="elementor-divider-separator"></div>
  </div>
</div>


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
       $id=$_SESSION["user_id"];
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
   
       $sql = "INSERT INTO order_item (user_id,street,zipcode,state) VALUES ('$user_id','$address','$zip', '$state');";
      
       if(mysqli_query($link, $sql))
       {

        $query1 = $link->query("SELECT ADDDATE(CURDATE(),INTERVAL $no_months MONTH) AS date_return");
        if($query->num_rows == 1){
        $row = $query1->fetch_assoc();
        $date_of_return = $row["date_return"];
        }
        echo "hello fff";
        // date of request: Timestamp when user enters queue
        // date of grant: When user accepts notif/request
        // date of return: When book is returned
        $get_queue_id_sql =  $link->query("SELECT queue_id FROM queue WHERE book_id='$book_id' AND user_id='$id' AND status='Pending'");
        $queue_id = $get_queue_id_sql->fetch_assoc()["queue_id"];
        $sql2 = "UPDATE queue SET date_granted=NOW(), date_of_return='$date_of_return', status='Currently Renting' WHERE queue_id=$queue_id";   
        
           if(mysqli_query($link, $sql2)) {   
               echo "tt";   
           $sql1 = "INSERT INTO payment(order_id,payment_date,payment_amount,mode_of_payment) VALUES (LAST_INSERT_ID(),NOW(),'$total','$mode')";
           if(mysqli_query($link, $sql1))
           {               
               $sql2= "UPDATE cart_item INNER JOIN book_for_rent on cart_item.book_id=book_for_rent.book_id SET cart_item.is_ordered=1, cart_item.order_id=LAST_INSERT_ID() WHERE cart_item.user_id=$user_id AND cart_item.is_ordered=0 AND cart_item.book_id NOT IN ( SELECT book_id FROM cart_item WHERE is_ordered=1)";
               if(mysqli_query($link, $sql2))
               {
                $sql3= "UPDATE book_for_rent SET is_available=0 WHERE book_id=$book_id";
                if(mysqli_query($link, $sql3))
                {
                    $event_sql= 
                    "CREATE EVENT `test_event_$queue_id`
                    ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL $no_months MINUTE
                    ON COMPLETION PRESERVE
                    DO 
                    BEGIN
                    declare no_of_waiting INT default 0;
                    declare next_waiting INT default 0;
                    set no_of_waiting = (select count(*) from queue where status='Waiting' and book_id=$book_id);
                    update queue set status='Returned' where queue_id=$queue_id;
                    if no_of_waiting>0 then
                    set next_waiting = (select queue_id from queue where status='Waiting' and book_id=$book_id limit 1);
                    update queue set status='Pending' where queue_id=next_waiting;
                    else
                    update book_for_rent set is_available=1 where book_id=$book_id limit 1;
                    end if;
                    END;";
                    if(mysqli_query($link, $event_sql))
                {
                    header("location: ../books/user_books.php");
                    echo "Payment Successful!..you will soon receive your book.";
                } else {
                     echo "ERROR: Could not able to execute $event_sql. " . mysqli_error($link);
                }
                }
                else{
                    echo "ERROR: Could not able to execute $sql3. " . mysqli_error($link);
                }
               }    
               else{
                   echo "ERROR: Could not able to execute $sql2. " . mysqli_error($link);
               }
           
           
           }       
           else{
               echo "ERROR: Could not able to execute $sql1. " . mysqli_error($link);
           }
           }  
           else{
               echo "ERROR: Could not able to execute $sql2. " . mysqli_error($link);
           }
         
    }       
       else{
           echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
       }
        
       // Close connection
       mysqli_close($link);
       
       }
?>
<script>
$(document).on('keyup', ".price",function () {
  var total = 0;
  
  $('.price').each(function(){
    total += parseFloat($(this).val());
  })  

  console.log(total)
})
</script>


<div class="row-checkout">
<div style="padding: 2%;" class="column-50 ">

    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post"> 
    <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label price">No of months:</label>
    <input  class="cu-form__input bg" type="number" name="no_months" required>
</div>
    
<div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Address:</label>
    <input  class="cu-form__input bg" type="text" name="address" required>
</div>
<div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Postcode / Zip:</label>
    <input type="text" name="zip" class="cu-form__input bg" required>
     </div>
<div class="login-page-new__main-form-row">
    <label  class="login-page-new__main-form-row-label">State:</label>   
    <input type="text" name="state"  class="cu-form__input bg" required>
    </div>

    <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>

</div>
<div style="padding: 2%;" class="column-50 ">
    <div style="font-size: 27px;" class="head-sub">Payment Method: </div>
          <div id="payment">
            <ul class=" payment_methods methods">
            <li class=" payment_method_bacs">
          <input id="payment_method_bacs" onclick="javascript:PaymentMethodCheck();" type="radio" class="input-radio" name="mode" value="Net Banking" checked="checked" data-order_button_text="">
          
          <label for="payment_method_bacs">
          Direct bank transfer 	</label>
            <div id="bank_transfer" class="payment_box payment_method_bacs" style="display: block;">
            <p class="payment-under">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.</p>
          </div>
          </li>
          <li class="payment_method_cod">
          <input id="payment_method_cod" onclick="javascript:PaymentMethodCheck();" type="radio" class="input-radio" name="mode" value="Cash on Delivery" data-order_button_text="">
          
          <label for="payment_method_cod">
          Cash on delivery 	</label>
            <div id="cod" class="payment_box payment_method_cod" style="display: none;">
            <p class="payment-under">Pay with cash upon delivery.</p>
          </div>
          </li>
          <li class="payment_method_paypal">
          <input id="payment_method_paypal" onclick="javascript:PaymentMethodCheck();" type="radio" class="input-radio" name="mode" value="Credit Card" data-order_button_text="Proceed to PayPal">
          
          <label for="payment_method_paypal">
          PayPal <img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg" alt="PayPal acceptance mark">	</label>
            <div id="paypal" class="payment_box payment_method_paypal" style="display: none;">
            <p class="payment-under">Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
          </div>
          </li>
          </ul>
</div>          
<input class="place-order-btn" type="submit" name="submit" value="Place Order">     
</form>
</div>
</div>
</body>
</html>