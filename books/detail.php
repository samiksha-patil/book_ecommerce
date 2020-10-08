<?php
// Include the database configuration file
include '../connection.php';
session_start();
$id=$_SESSION["user_id"];

print_r($_POST); 
if($_SERVER["REQUEST_METHOD"] == "GET"){
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
}

function add_to_queue()
{
    
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(isset($_POST["id"]) && !empty($_POST["id"]) && isset($_POST["no_of_months"]) && !empty($_POST["id"]) )
    {
        $book_id = $_POST['id'];
        $no_of_months = $_POST["no_of_months"];
        $query1 = $link->query("SELECT ADDDATE(CURDATE(),INTERVAL $no_of_months MONTH ) AS date_return ");
        if($query->num_rows == 1){
        $row = $query1->fetch_assoc();
        $date_of_return = $row["date_return"];
        }
        echo $date_of_return;

        $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $id AND is_ordered=0");
        if($query->num_rows == 0){
            
        $sql = "INSERT INTO queue (user_id, book_id, status, date_of_request , date_granted, date_of_return) VALUES ('$user_id','$book_id','waiting',NOW(),NOW(),'$date_of_return')";
        if(mysqli_query($link, $sql)){
            $query = $link->query("SELECT * FROM book_for_rent WHERE book_id = $book_id");
            if($query->num_rows > 0){
                header("location: ../order/payment_rent.php?id=$book_id");
            }
            else {
                header("location: ../books/detail.php?id=$book_id");
            }
        } 
        else{
                echo "Oops! Something went wrong. Please try again later.",$sql,mysqli_error($link);
            }
        }
        else{
            echo "Book already exists in your cart";
        }
        mysqli_close($link);
    }
    else{
        if(empty(trim($_POST["id"]))){
            header("location: ../error.php");
            exit();
        }
    }   
}
}
if(isset($_POST['add_to_queue']))
{
    echo "hello";
   add_to_queue();
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

                        <form action="detail.php" method="post">
                        <input type="text" name="no_of_months">
                        <input type="hidden" name="id" value="<?php echo $param_id;?>">
                        <input type="submit" name="add_to_queue">
                        </form>
                        
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