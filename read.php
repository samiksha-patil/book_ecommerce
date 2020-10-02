<?php
// Include the database configuration file
include 'connection.php';
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){    

    $param_id = trim($_GET["id"]);
    $type="";
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
    }
    else {
        header("location: error.php");
        exit();
    }
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
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
                        <?php if($rent_row["is_available"]) {?> Available 
                        <?php } else { ?> Rented <?php } ?>
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
                    <div class="form-group">
                        <?php if($sale_row["cart_item_id"]) {?> Sold out 
                        <?php } else ?> Available
                    </div>
                    <?php } ?>
                    <p><a href="user_books.php" class="btn btn-primary">Back</a></p>
                    <?php if($row["user_id"]== $_SESSION["user_id"]){
                   
                    echo "<a href='update.php?id=". $row['book_id'] ."'class='btn btn-info'>Update</a>";                    
                    echo "<a href='book_delete.php?id=". $row['book_id'] ."'class='btn btn-danger'>Delete</a>";
                    
                     } else{ ?>
                        <p><a href="" class="btn btn-info">Add to cart</a></p>
                        <?php } ?>
                </div>
                <img src="uploads/<?php echo $row["cover_image"]; ?>" height="200px" >
            </div>        
        </div>
    </div>
</body>
</html>