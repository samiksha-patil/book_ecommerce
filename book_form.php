<?php

session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "connection.php";

$title_err = $type_err=$price_err=$rate_err="";
$type =$title="";
$sale = 'sale';
$rent ='rent';

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    if((empty(trim($_POST["title"]))))
    {
        $title_err = "enter the title of the book";
       
           
    }
    else{
        $title = $_POST['title'];
    }

    if((empty($_POST["type"])))
    {
        $type_err = "please select the type of book";  
    }
    else{
       
        $type = $_POST['type'];
            if($type==$sale){
                if(empty(trim($_POST['price']))){
                $price_err = "please enter the price"; 
                }
            else{
                $price =$_POST['price'];
            }
        }
            
            if($type==$rent){
                if(empty(trim($_POST['rate']))){
                $rate_err = "please enter the monthly rate";
                } 
                  
            else{
                $rate =$_POST['rate'];
            }
        }
            
        }

// File upload path
$targetDir = "uploads/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

$allowTypes = array('jpg','png','jpeg','gif','pdf');

$info =$_POST['info'];
$id=$_SESSION["user_id"];
$author = $_POST['author'];
$language =$_POST['language'];
$pages = $_POST['pages'];
$rate = $_POST['rate'];
$discount_price = $_POST['discount_price'];
$price =$_POST['price'];


// Attempt insert query execution
if((empty($title_err) && empty($type_err)) && (empty($rate_err) && empty($price_err)) )
{
move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath);
$sql = "INSERT INTO book (user_id,title, cover_image, info, author, lang, no_of_pages) VALUES ('$id','$title','".$fileName."', '$info', '$author', '$language', '$pages')";

if(mysqli_query($link, $sql))
{
    if($type==$sale)
    {
    $sql1 = "INSERT INTO book_for_sale (book_id,price, discount_price) VALUES ( LAST_INSERT_ID(),'$price','$discount_price')";
    if(mysqli_query($link, $sql1))
    {
    echo "Book added successfully.";
    header("location: user_books.php");
    }
    } 
    if($type==$rent)
    {
    $sql1 = "INSERT INTO book_for_rent (book_id,monthly_rate) VALUES ( LAST_INSERT_ID(),'$rate')";
    if(mysqli_query($link, $sql1))
    {
    echo "Book added successfully.";
    header("location: user_books.php");
    } 
}}
else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// Close connection
mysqli_close($link);
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
   
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
    <label>Title:</label>
    <input type="text" name="title" class="form-control">
    <span class="help-block"><?php echo $title_err; ?></span>
    </div>
    <label>Description:</label>
    <input type="text" name="info" class="form-control">
    <input type="file" name="file">
    <label>Author:</label>
    <input type="text" name="author" class="form-control">
    <label>Language:</label>
    <input type="text" name="language" class="form-control" >
    <label>No. of pages:</label>
    <input type="text" name="pages" class="form-control" >
    <br><br>
    <div class="form-group <?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
    <label>rent</label><input type="radio"  name="type" value ="rent" id="rent" onclick="javascript:BooktypeCheck();"> 
    <label>sale</label><input type="radio" name="type" value="sale" id="sale"onclick="javascript:BooktypeCheck();">
    <span class="help-block"><?php echo $type_err; ?></span><br>
    </div>
<div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $price_err; ?></span>
<div id="for_sale">
    <label>Price:</label>
    <input type="text" name="price" class="form-control" >
    <label>Discount Price:</label>
    <input type="text" name="discount_price" class="form-control" >
    </div>   
</div>
<div class="form-group <?php echo (!empty($rate_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $rate_err; ?></span>
<div id="for_rent">
    
    <label>Monthly rate:</label>
    <input type="text" name="rate" class="form-control" >
    </div>
      
</div>
<input type="submit" name="submit" value="Upload">
       
</form>
<script>

document.getElementById('for_sale').style.display = 'none';
document.getElementById('for_rent').style.display = 'none';


        function BooktypeCheck() {
            if (document.getElementById('sale').checked) {
                document.getElementById('for_sale').style.display = 'block';
                document.getElementById('for_rent').style.display = 'none';
            }
            else if (document.getElementById('rent').checked) {
              
                document.getElementById('for_sale').style.display = 'none'
                document.getElementById('for_rent').style.display = 'block';
            }
            
        }
</script>
</body>
</html>