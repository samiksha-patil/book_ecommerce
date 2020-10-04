<?php

// Include the database configuration file
include '../connection.php';

$title_err = $type_err=$price_err=$rate_err="";
$type="";

session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}


// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){    
    $id=$_SESSION["user_id"];
    $param_id = trim($_GET["id"]);
    // Get images from the database
    $query = $link->query("SELECT * FROM book WHERE book_id = $param_id AND user_id=$id");
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        $category = $row["category"];
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
        header("location: ../welcome.php");
        exit();
    }
}

if(isset($_POST["id"]) && !empty(trim($_POST["id"])) && ($_SERVER["REQUEST_METHOD"] == "POST"))
{
    $param_id = trim($_POST["id"]);
    if((empty(trim($_POST["title"])))) $title_err = "enter the title of the book";
    else $title = $_POST['title'];

    if((empty($_POST["type"])))
        $type_err = "please select the type of book";  
    else {
        $type = $_POST['type'];
            if($type=="sale")
                if(empty(trim($_POST['price'])))
                    $price_err = "please enter the price"; 
                else
                    $price =$_POST['price'];
            
            if($type=="rent")
                if(empty(trim($_POST['rate'])))
                    $rate_err = "please enter the monthly rate";                  
                else
                    $rate =$_POST['rate'];            
    }

    // File upload path
    $targetDir = "../uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

    $allowTypes = array('jpg','png','jpeg','gif','pdf');

    $info =$_POST['info'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $language =$_POST['language'];
    $pages = $_POST['pages'];

    move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath);
    $sql = "UPDATE book SET title='$title', info='$info', author='$author', lang='$language', no_of_pages=$pages, category='$category' WHERE book_id=$param_id";
    if($query = $link->query($sql))
    {
        if(!empty($fileName)) {
            $sql = "UPDATE book SET cover_image='$fileName' WHERE book_id=$param_id";
            $query = $link->query($sql);
        }
        if($type=="sale") {
            echo $sql;
            $discount_price = $_POST['discount_price'];
            $price =$_POST['price'];
            $sql1 = "UPDATE book_for_sale SET price=$price, discount_price=$discount_price WHERE book_id=$param_id";
            if(mysqli_query($link, $sql1))
                echo "Records added successfully.";
        } 
        if($type=="rent") {
            $rate = $_POST['rate'];
            $sql1 = "UPDATE book_for_rent SET monthly_rate=$rate WHERE book_id=$param_id";
            if(mysqli_query($link, $sql1))
                echo "Book added successfully.";
        }
        else {
            header("location: ../error.php");
        }
        header("location: user_books.php");
        $query -> free_result();
    }
    else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    mysqli_close($link);
} else {

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
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
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                                        
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $param_id; ?>"/>
                        <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                        <label>Title:</label>
                        <input type="text" name="title" class="form-control" required value="<?php echo $row["title"]; ?>">
                        <span class="help-block"><?php echo $title_err; ?></span>
                        </div>
                        <label>Description:</label>
                        <input type="text" name="info" class="form-control" required value="<?php echo $row["info"]; ?>">
                        <label>Cover Image:</label>
                        <img src="../uploads/<?php echo $row["cover_image"]; ?>" height="200px" >
                        <input type="file" name="file" value="<?php echo $row["cover_image"]; ?>">
                        <label for="category">Category:</label>
                        <select  id="category" name="category">
                        <option value="Textbook">Textbook</option>
                        <option value="Memoir">Memoir</option>
                        <option value="Essays">Essays</option>
                        <option value="Action and Adventure">Action and Adventure</option>
                        <option value="Classics">Classics</option>
                        <option value="Comic Book or Graphic Novel">Comic Book or Graphic Novel</option>
                        <option value="Fiction">Fiction</option>
                        </select>
                        <br>
                        <label>Author:</label>
                        <input type="text" name="author" class="form-control"required value="<?php echo $row["author"]; ?>">
                        <label>Language:</label>
                        <input type="text" name="language" class="form-control" required value="<?php echo $row["lang"]; ?>">
                        <label>No. of pages:</label>
                        <input type="number" name="pages" class="form-control" required value="<?php echo $row["no_of_pages"]; ?>">
                        <br><br>
                        <?php if($type=="sale") { ?>
                        <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                        <span class="help-block"><?php echo $price_err; ?></span>
                        <div id="for_sale">
                            <label>Price:</label>
                            <input type="text" name="price" id="price" class="form-control" required value="<?php echo $sale_row["price"]; ?>">
                            <label>Discount Price:</label>
                            <input type="text" name="discount_price" class="form-control" required value="<?php echo $sale_row["discount_price"]; ?>">
                            </div>   
                        </div>
                        <?php } else { ?>
                        <div class="form-group <?php echo (!empty($rate_err)) ? 'has-error' : ''; ?>">
                        <span class="help-block"><?php echo $rate_err; ?></span>
                        <div id="for_rent">
                            <label>Monthly rate:</label>
                            <input type="text" id="rate" name="rate" class="form-control" required value="<?php echo $rent_row["monthly_rate"]; ?>">
                        </div>
                        <?php } ?>
                        </div>
                        <input type="submit" name="submit" value="Update" class="btn btn-primary" id="submit"> 
                    <a href="user_books.php" class="btn btn-danger">Back</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
    <script>
    document.getElementById("category").value ='<?php echo $category ?>';
    </script>
</body>
</html>