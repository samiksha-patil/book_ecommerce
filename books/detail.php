<?php
// Include the database configuration file
include '../connection.php';
session_start();
$id=$_SESSION["user_id"];
 
// Check if the user is already logged in, if yes then redirect him to welcome page

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){    

    $param_id = trim($_GET["id"]);
    $type="";
    $available=true;
    $query = $link->query("SELECT * FROM book WHERE book_id = $param_id");
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        $query = $link->query("SELECT * FROM book_for_rent WHERE book_id = $param_id");
        if($query->num_rows > 0){
            $type="rent";
            $rent_row = $query->fetch_assoc();
        }
        else {
            $query = $link->query("SELECT * FROM book_for_sale WHERE book_id = $param_id");
            if($query->num_rows > 0){
                $type="sale";
                $sale_row = $query->fetch_assoc();
            }
        }
        $query = $link->query("SELECT is_ordered FROM cart_item WHERE book_id = $param_id AND is_ordered = True");
        if($query->num_rows > 0){
            $available=false;
        }
    }
    else {
        header("location: ../error.php");
        exit();
    }
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: ../error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1><?php echo $row["title"]; ?></h1>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <p class="form-control-static"><?php echo $row["info"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <p class="form-control-static"><?php echo $row["category"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <p class="form-control-static"><?php echo $row["author"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Lanuage</label>
                        <p class="form-control-static"><?php echo $row["lang"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>No. of pages</label>
                        <p class="form-control-static"><?php echo $row["no_of_pages"]; ?></p>
                    </div>
                    <?php if($type=="rent") { ?>
                    <div class="form-group">
                        <label>Rate per month</label>
                        <p class="form-control-static"><?php echo $rent_row["monthly_rate"]; ?></p>
                    </div>
                    <div class="form-group">
                        <?php if($rent_row["is_available"]) {?> 
                        
                        Available 

                        <?php } else { ?> 
                        
                        Rented 
                        
                        <?php } ?>
                    </div>
                    <?php } else { ?>
                    <div class="form-group">
                        <label>Price</label>
                        <p class="form-control-static"><?php echo $sale_row["price"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Discount Price</label>
                        <p class="form-control-static"><?php echo $sale_row["discount_price"]; ?></p>
                    </div>
                    <?php } ?>
                    <p><a href="user_books.php" class="btn btn-primary">Back</a></p>
                    <?php if($row["user_id"]==$_SESSION["user_id"]){
                   
                    echo "<a href='update.php?id=". $row['book_id'] ."'class='btn btn-info'>Update</a>";                    
                    echo "<a href='delete.php?id=". $row['book_id'] ."'class='btn btn-danger'>Delete</a>";
                    
                     } 
                     else{
                         
                        if($available) {?> 
                        <div class="form-group">
                        Available
                        </div>
                        <?php 
                        
                        $in_cart = false;
                        $book_id = $row["book_id"];
                        $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $id");
                        if($query->num_rows > 0){ 
                            echo '<a href="../order/cart.php" class="btn btn-primary">View in cart</a>';
                        } 
                        else { 
                            echo "<a href='../order/add_to_cart.php?id=". $row['book_id'] ."'class='btn btn-info'>Add to Cart</a>";
                        } 
                        ?>
                        <?php } else { ?> 
                        <div class="form-group">
                        Sold Out
                        </div>
                        <?php 
                        
                        $in_cart = false;
                        $book_id = $row["book_id"];
                        $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $id");
                        if($query->num_rows > 0){ 
                            echo '<button disabled class="btn btn-secondary">Already Bought</button>';
                        } 
                        else { 
                            echo '<button disabled class="btn btn-secondary">Add to Cart</button>';
                        } 
                        ?>
                        <?php }
                    } ?>
                </div>
                <img src="../uploads/<?php echo $row["cover_image"]; ?>" height="200px" >
            </div>        
        </div>
    </div>
</body>
</html>