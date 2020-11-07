<?php 
    include "../components/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
        <link rel="stylesheet" href="../static/css/main.css">
    <link rel="stylesheet" href="../static/css/styles.css">
    <link rel="stylesheet" href="../static/css/user-books.css">
    <link rel="stylesheet" href="../static/css/form.css" />
<link rel="stylesheet" href="../static/css/cart.css" />
<link rel="stylesheet" href="../static/css/checkout.css" />
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

     <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<body>
<body>
<div class="container-top">
    <img class="img-top" src="../static/images/title-img.jpg" alt="Cinque Terre" width="1000" height="300">
    <div style="font-size:60px;" class="center product-title">My Orders<div class="elementor-divider-separator"></div></div>
  
</div>
       
                    <?php
                    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                        header("location: ../accounts/login.php");
                        exit;
                    }
                    $id=$_SESSION["user_id"];
            
                    $sql1= "SELECT * FROM order_item where user_id=$id";
                    if($result = mysqli_query($link, $sql1)){
                        if(mysqli_num_rows($result) > 0){ ?>
                     
                            <?php 
                           
                            while($row1 = mysqli_fetch_array($result)){ 
                               $order_id= $row1["order_id"];
                ?> 
                 <div class="card card-custom ">
                    
<div style="padding:40px 0px;" class="row">
<div class="column-50">

        
  <div style="text-align: center;" class="head-sub">Order Summary</div>
<table style="width:100%; max-width: 800px;">
      
<tbody>
<tr>
   <th></th>
   <th>Title</th>
   <th>Sub Total</th>
   </tr>
<?php
                            $sql= "SELECT * FROM cart_item natural join book_view  where order_id=$order_id";
                            if($result1 = mysqli_query($link, $sql)){
                                if(mysqli_num_rows($result1) > 0){ ?>
                                  
                                    <?php 
                           
                            while($row = mysqli_fetch_array($result1)){
                                $type = $row["type"];
                                         ?>
                                        <tr>
                                        <td>  <img style="height: 65px; width: auto;" class="img-book" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="" /></td>
                                          <td><?php echo $row["title"]; ?></td>
                                          <td>Rs. <?php echo $row["price"] ?></td>
                                        </tr>  
                                                
                                             
                                    <?php }                             
                                }
                            }




                                $sql2= "SELECT * FROM payment where order_id=$order_id";
                            if($result2 = mysqli_query($link, $sql2)){
                                if(mysqli_num_rows($result2) > 0){ ?>
                                  
                                    <?php 
                           
                            while($row2 = mysqli_fetch_array($result2)){ ?>
                                  
                                  <tr >
    <td style=" font-size: 27px;" class="head-sub">Total:</td><td></td> <td style=" font-size: 27px;" class="head-sub">$<?php echo $row2["payment_amount"]; ?></td></div>
    </tr>
    </tbody>
    </table>
    </div>
   <div style="margin:auto; padding:30px;" class="column-50">
   <div >
   <div style="width:fit-content; padding: 0.6rem 1rem; font-size: 25px;" class="bt-status-avaliable"><?php echo $type ?></div>
   <div class="order-pay">  Payment mode: <?php echo $row2["mode_of_payment"]; ?></div>
   <div class="order-pay">  Date: <?php echo $row2["payment_date"] ?></div>
   <div class="order-pay">  Address: gqiuhsyi fhy8 idH FJG WDGS</div>
   </div>
   </div>                             
                                                 
                                              
                                    <?php }                             
                                }} ?>


                                </div>

                                </div>
                                    
                                

                                <?php
                            }
                               
              
                    
                                   
                               
                           
                               
                          
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
            
</body>
</html>