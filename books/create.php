<?php

session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

require_once "../connection.php";

$title_err = $type_err=$price_err=$rate_err="";
$type =$title="";

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
            if($type=="sale"){
                if(empty(trim($_POST['price']))){
                $price_err = "please enter the price"; 
                }
            else{
                $price =$_POST['price'];
            }
        }
            
            if($type=="rent"){
                if(empty(trim($_POST['rate']))){
                $rate_err = "please enter the monthly rate";
                } 
                  
            else{
                $rate =$_POST['rate'];
            }
        }
            
        }

// File upload path
$targetDir = "../uploads/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

$allowTypes = array('jpg','png','jpeg','gif','pdf');

$id=$_SESSION["user_id"];
$info =$_POST['info'];
$category = $_POST['category'];
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
$sql = "INSERT INTO book (user_id,title, cover_image, info, author, lang, no_of_pages, category) VALUES ('$id','$title','".$fileName."', '$info', '$author', '$language', '$pages', '$category')";

if(mysqli_query($link, $sql))
{
    if($type=="sale")
    {
    $sql1 = "INSERT INTO book_for_sale (book_id, price) VALUES (LAST_INSERT_ID(),'$price')";
    if(mysqli_query($link, $sql1))
    {
    echo "Book added successfully.";
    header("location: user_books.php");
    }
    } 
    if($type=="rent")
    {
    $sql1 = "INSERT INTO book_for_rent (book_id,monthly_rate) VALUES (LAST_INSERT_ID(),'$rate')";
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
    <input type="text" name="title" class="form-control" required>
    <span class="help-block"><?php echo $title_err; ?></span>
    </div>
    <label>Description:</label>
    <input type="text" name="info" class="form-control" required>
    <label for="cover_image">Cover Image"</label>
    <input type="file" name="file">
    <label for="category">Category"</label>
    <select name="category">
    <option value="Textbook">Textbook</option>
    <option value="Memoir">Memoir</option>
    <option value="Essays">Essays</option>
    <option value="Action and Adventure">Action and Adventure</option>
    <option value="Classics">Classics</option>
    <option value="Comic Book or Graphic Novel">Comic Book or Graphic Novel</option>
    <option value="Fiction">Fiction</option>
    </select>
    <label>Author:</label>
    <input type="text" name="author" class="form-control"required>
    <label>Language:</label>
    <input type="text" name="language" class="form-control" required>
    <label>No. of pages:</label>
    <input type="number" name="pages" class="form-control" required>
    <br><br>
    <div class="form-group <?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
    <label>rent</label><input type="radio" name="type" value="rent" id="rent" onclick="javascript:BooktypeCheck();" required> 
    <label>sale</label><input type="radio" name="type" value="sale" id="sale" onclick="javascript:BooktypeCheck();" required>
    <span class="help-block"><?php echo $type_err; ?></span><br>
    </div>
<div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $price_err; ?></span>
<div id="for_sale">
    <label>Price:</label>
    <input type="text" name="price" id="price" class="form-control" required>
    </div>   
</div>
<div class="form-group <?php echo (!empty($rate_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $rate_err; ?></span>
<div id="for_rent">
    
    <label>Monthly rate:</label>
    <input type="text" id="rate" name="rate" class="form-control" required>
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
                document.getElementById('rate').required = false;
                document.getElementById('price').required = true;
            }
            else if (document.getElementById('rent').checked) {
                document.getElementById('for_sale').style.display = 'none'
                document.getElementById('for_rent').style.display = 'block';
                document.getElementById('price').required = false;
                document.getElementById('rate').required = true;
            }
            
        }
</script>
</body>
</html>