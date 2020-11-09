<?php

include '../components/navbar.php'; 



if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

$id=$_SESSION["user_id"];
?>
           
           
<!DOCTYPE html>
<html lang="en">
<head>
 
<meta charset="UTF-8">   
<link rel="stylesheet" href="../static/css/main.css">
<link rel="stylesheet" href="../static/css/cart.css">


<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<div class="container-top">
    <img class="img-top" src="../static/images/title-img.jpg" alt="Cinque Terre" width="1000" height="300">
    <div class="center product-title">Cart<div class="elementor-divider-separator"></div></div>
    
</div>
<div class="card main-raised">
    
<div class="table-responsive">
    
       
                 

    
                    <?php
                   
                    $sql = "SELECT case when discount_price<>null or discount_price<>0 then discount_price else price end as price, cover_image, title, book.book_id, category FROM book NATURAL JOIN book_for_sale INNER JOIN cart_item on cart_item.book_id= book_for_sale.book_id WHERE cart_item.user_id=$id AND cart_item.is_ordered=0";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){ ?>
 <table class="table table-shopping">
          <thead>
            <tr>
              <th class="product-title">Image</th>
              <th class="product-title">Title</th>
              <th class="product-title">Category</th>
              <th class="product-title">Price</th>
              <th ></th>
            </tr>
          </thead>
          <tbody>
              
          
                            <?php 
                            $total =0;
                            while($row = mysqli_fetch_array($result)){ ?>

                    <?php
                    $book_id =$row["book_id"];
                    $sql1 = "SELECT * FROM cart_item  WHERE cart_item.book_id=$book_id AND cart_item.is_ordered=1";
                    $sold = False;
                    if($result1 = mysqli_query($link, $sql1)){
                       
                        if(mysqli_num_rows($result1) >0){
                            $sold = True;
                        }
                        else{
                        $total = $total+ $row["price"];
                    
                    }

                  }
                    ?>
                    
                  


                               <tr <?php if($sold){?>
                     style="background-color: #c9c9d326;"
                 <?php   } ?>    class="cart-element">
                               <td  class="align-center" <?php if($sold){?>
                     style='opacity: 0.4;'
                 <?php   } ?>>
                               <img style="height: 150px; padding-bottom: 10px;" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="" />
              
                                </td>
                                <td class="td-name"  <?php if($sold){?>
                     style='opacity: 0.4;'
                 <?php   } ?>>
                                               
                                <?php echo $row["title"]; ?>
                                </td>
                                <td <?php if($sold){?>
                     style='opacity: 0.4;'
                 <?php   } ?>>
                                <?php echo $row["category"] ?>
                                </td>
                                <td <?php if($sold){?>
                     style='opacity: 0.4;'
                 <?php   } ?>>
                                $ <?php echo $row["price"] ?>
                                </td>
                                
                                <td>
                                <?php if($sold){?>
                   <p style="" class="sold-out align-center">  sold out</p>
                 <?php   } ?>
                                 <a class="close-icon" href="remove_from_cart.php?id=<?php echo $row["book_id"] ?>" ><ion-icon name="close-outline"></ion-icon></a>
                                </td>
                                </tr>
                                                   
                                                    
                                                
                                    <?php }                             
                            
                                ?>          
       
</tbody>
        </table>
                               
      </div>
      
      <div class="checkout-btn">
        <div style="margin-bottom: 1rem" class="head-sub">Total	 $<?php echo "$total" ?></div>
        <a href="checkout.php">PROCEED TO CHECKOUT</a>
      
    </div>
      
    </div>
                          
                                    
                                    <?php
                               
                           
                               
                          
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            ?>
                     <p style="font-size:20px;margin-bottom:20px;" class="align-center">    No items in your cart </p>

                           <?php
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>


</body>
</html>













