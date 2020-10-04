<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
        .books img {
            height: 200px
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Books for rent</h2>
                    </div>
                    <?php
                    include '../connection.php';
                    session_start();
                    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                        header("location: ../accounts/login.php");
                        exit;
                    }
                    $id=$_SESSION["user_id"];
                    $sql = "SELECT * FROM book NATURAL JOIN book_for_rent INNER JOIN cart_item on cart_item.book_id= book_for_rent.book_id WHERE cart_item.user_id=$id";
                   
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){ ?>
                        <div class="row books">
                        <?php while($row = mysqli_fetch_array($result)){ ?>
                                    <div class="col-sm-3">
                                    <a href="../books/detail.php?id=<?php echo $row["book_id"] ?>">
                                        <div class="card" style="width: 18rem;">
                                            <img class="card-img-top" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="Card image cap" >
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $row["title"]; ?></h5>
                                                <p class="card-text">Rs. <?php echo $row["monthly_rate"] ?>/month</p>
                                                <a href="remove_from_cart.php?id=<?php echo $row["book_id"] ?>" class="btn btn-primary">Remove from cart</a>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                <?php } ?>
                    </div>
                    <?php
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
                    ?>
                </div>
            </div>        
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Books for sell</h2>
                       
                    </div>
                    <?php
                   
                    $sql = "SELECT * FROM book NATURAL JOIN book_for_sale INNER JOIN cart_item on cart_item.book_id= book_for_sale.book_id WHERE cart_item.user_id=$id AND cart_item.is_ordered=0";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){ ?>
                            <div class="row books">
                            <?php 
                            $total =0;
                            while($row = mysqli_fetch_array($result)){ ?>
                                        <div class="col-sm-3">
                                            <div class="card" style="width: 18rem;">
                                                <img class="card-img-top" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="Card image cap" >
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $row["title"]; ?></h5>
                                                    <p class="card-text">Rs. <?php echo $row["price"] ?></p>
                                                    <a href="remove_from_cart.php?id=<?php echo $row["book_id"] ?>" class="btn btn-primary">Remove from cart</a>
                                                    <?php
                                                    $book_id =$row["book_id"];
                                                    $sql1 = "SELECT * FROM cart_item  WHERE cart_item.book_id=$book_id AND cart_item.is_ordered=1";
                                                    
                                                    if($result1 = mysqli_query($link, $sql1)){
                                                       
                                                        if(mysqli_num_rows($result1) >0){
                                                            echo "sold out";
                                                        }
                                                        else{
                                                        $total = $total+ $row["price"];
                                                        }

                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }                             
                            
                                ?>
                        </div>
                        
                                  
                                    <h3 class="card-title"> Total:  <?php echo "$total" ?></h3>
                                    <a href="checkout.php" class="btn btn-warning">PROCEED TO CHECKOUT</a>
                                    <?php
                               
                           
                               
                          
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
                </div>
            </div>        
        </div>


</body>
</html>