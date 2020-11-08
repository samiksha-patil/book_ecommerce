<?php 
    include "../components/navbar.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

$id= $_SESSION["user_id"];
$payment_success=false;


$sql = "SELECT sum(p) as total from (SELECT cart_item.user_id, case when discount_price<>null or discount_price<>0 then discount_price else price end as p, price, discount_price FROM book NATURAL JOIN book_for_sale INNER JOIN cart_item on cart_item.book_id= book_for_sale.book_id WHERE cart_item.user_id=$id AND cart_item.is_ordered=0) as s";
if($res = mysqli_query($link, $sql)){
  if(mysqli_num_rows($res) > 0){ 
      $total = mysqli_fetch_array($res)["total"];
  }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
$id=$_SESSION["user_id"];;
$address = $_POST['address'];
$zip = $_POST['zip'];
$state =$_POST['state'];
$mode = $_POST['mode'];


// $sql2 ="LOCK tables book read";
// if(mysqli_query($link, $sql2))
// {
$sql = "INSERT INTO order_item (user_id,street,zipcode,state) VALUES ('$id','$address','$zip', '$state')";


if(mysqli_query($link, $sql))
{   
  
  // $sql4= "UNLOCK TABLES";
        
  // if(mysqli_query($link, $sql4))
  // {
                
    $sql1 = "INSERT INTO payment(order_id,payment_date,payment_amount,mode_of_payment) VALUES (LAST_INSERT_ID(),NOW(),'$total','$mode')";
    if(mysqli_query($link, $sql1))
    {
        
        $sql3= "UPDATE cart_item SET cart_item.is_ordered=1, cart_item.order_id=LAST_INSERT_ID()  WHERE cart_item.user_id=$id AND cart_item.is_ordered=0 AND cart_item.book_id  IN ( SELECT book_id FROM book_for_sale) AND cart_item.book_id NOT IN ( SELECT book_id FROM cart_item WHERE is_ordered=1)";
        
        if(mysqli_query($link, $sql3))
        {
         
  
            
          $payment_success=true;
          }
        }
    }
    
  //} 
  // else{
  //   echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
  //   $sql4= "UNLOCK TABLES";
        
  // if(mysqli_query($link, $sql4))
  // {
  //   echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
  // }
//}

//}
else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
  
// Close connection


}
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
   
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<?php 
if($payment_success) { ?>
<script>
    
Swal.fire({
  text: "Payment Successful!",
  icon: 'success',
  confirmButtonText: 'Okay'
}).then((result) => {
  if (result.isConfirmed) {
    window.location.href="../order/my_orders.php";
  }
})
</script>
<?php } ?>
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


<div class="row-checkout">


<div style="padding: 2%;" class="column-50 ">
    
  <div style="text-align: center;" class="head-sub">Order Summary</div>
   <table style="width:100%; max-width: 800px;">
         
   <tbody>
   <tr>
   <th></th>
   <th>Title</th>
   <th>Sub Total</th>
   </tr>
   
        
<?php


$sql5 = "SELECT case when discount_price<>null or discount_price<>0 then discount_price else price end as price, cover_image, title, book.book_id FROM book NATURAL JOIN book_for_sale INNER JOIN cart_item on cart_item.book_id= book_for_sale.book_id WHERE cart_item.user_id=$id AND cart_item.is_ordered=0";
                    
                    if($result5 = mysqli_query($link, $sql5)){
                        if(mysqli_num_rows($result5) > 0){ ?>
                            
                            <?php 
                            while($row1 = mysqli_fetch_array($result5)){ ?>
                                                    <tr>
                                          
                                                    <?php
                                                    $book_id =$row1["book_id"];
                                                    $sql6 = "SELECT * FROM cart_item  WHERE cart_item.book_id=$book_id AND cart_item.is_ordered=1";
                                                    
                                                    if($result6 = mysqli_query($link, $sql6)){
                                                       
                                                        if(mysqli_num_rows($result6)<1){
                                                            
                                                        
                                                    ?>
                                                    <td>  <img style="height: 65px; width: auto;" class="img-book" src="../uploads/<?php echo $row1["cover_image"]; ?>" alt="" /></td>
                                                   <td> <?php echo $row1["title"]; ?> </td>
                                                    <td>$ <?php echo $row1["price"] ?></td></tr>
                                                     <?php  
                                                        }

                                                    }
                                                    ?>
                                             
                                    <?php }                             
                            
                                ?>
                     
    <tr >
    <td style=" font-size: 27px;" class="head-sub">Total:</td><td></td> <td style=" font-size: 27px;" class="head-sub">$<?php echo "$total" ?></td></div>
    </tr>
    </tbody>
    </table>

</div>
<div style="padding: 2%;" class="column-50">
      <div  class="head-sub">Shipping Details: </div>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="checkout_form">
<div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Address:</label>
    <textarea name="address" form="checkout_form" required></textarea>
</div>
   <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Postcode / Zip:</label>
    <input type="text" name="zip" class="cu-form__input bg" required>
     </div>
<div class="login-page-new__main-form-row">
    <label  class="login-page-new__main-form-row-label">State:</label>   
    <input type="text" name="state"  class="cu-form__input bg" required>
    </div>

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
</div>
                        
                            
                                    
                                    <?php
                               
                           
                               
                          
                            // Free result set
                            mysqli_free_result($result5);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    mysqli_close($link);
                   
                    ?>


  <script>
document.getElementById('bank_transfer').style.display = 'none';
    document.getElementById('cod').style.display = 'none';
    document.getElementById('paypal').style.display = 'none';
    
    
            function PaymentMethodCheck() {
                if (document.getElementById('payment_method_bacs').checked) {
                  document.getElementById('bank_transfer').style.display = 'block';
                  document.getElementById('cod').style.display = 'none';
                  document.getElementById('paypal').style.display = 'none';
                  
                }
                else if (document.getElementById('payment_method_cod').checked) {
                  document.getElementById('bank_transfer').style.display = 'none';
                  document.getElementById('cod').style.display = 'block';
                  document.getElementById('paypal').style.display = 'none';
                }
                else if (document.getElementById('payment_method_paypal').checked) {
                  document.getElementById('bank_transfer').style.display = 'none';
                  document.getElementById('cod').style.display = 'none';
                  document.getElementById('paypal').style.display = 'block';
                }
                
            }
</script>

