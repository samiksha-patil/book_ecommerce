<?php

 include '../components/navbar.php'; 
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
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../static/css/form.css">
<link rel="stylesheet" href="../static/css/styles.css">

<script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
<style>
    .thumb-image{
width:90%;
 position:relative;
 padding:5px;
 margin: auto;
}
.hide_file {
    position: absolute;
    z-index: 1000;
    opacity: 0;
    cursor: pointer;
    right: 0;
    top: 0;
    height: 100%;
    font-size: 24px;
    width: 100%;
    
}
.abc{
  padding:15px 15px;
  background:#F7F7F7;
  border:1px solid #ff7962fc;
  position:relative;
 
  border-radius:2px;
  text-align:center;
  float:left;
  cursor:pointer
}
</style>
</head>

<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="create_form">
<div class="registration-container" style="max-width:1020px;">
  <div class="login-row" >
      <div style="margin:auto;">
   <div >   
    <div class="abc  login-page-new__main-form-row-label" for="cover_image">Cover Image <ion-icon name="cloud-upload-outline"></ion-icon>

    <input  id="fileUpload" class="hide_file" type="file"  name="file">
    </div>
    </div>
    <div style="margin:auto;"> 
      <div  id="image-holder"> </div>
</div></div>
  <div class="">


  <div style="padding: 30px 60px 0px 26px ;" >
        <div class="login-page-new__main-form-title">Add a Book</div>
            <div class="login-page-new__main-form-row">


    <div class="<?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
    <label class="login-page-new__main-form-row-label" >Title</label>
    <input class="cu-form__input" type="text" name="title" required>
    <span class="help-block"><?php echo $title_err; ?></span>
    </div>
    </div>
    <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Description</label>
    
    <textarea class="cu-form__input" name="info" form="create_form" required></textarea>
    </div>
    
    <div style="padding:0px 0px;" class="row">
    <div class="column-50 p-r">
    <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label" for="category">Category</label>
    <select style="margin-top:7px;" id="category" name="category">
    <option value="Textbook">Textbook</option>
    <option value="Memoir">Memoir</option>
    <option value="Essays">Essays</option>
    <option value="Action and Adventure">Action and Adventure</option>
    <option value="Classics">Classics</option>
    <option value="Comic Book or Graphic Novel">Comic Book or Graphic Novel</option>
    <option value="Fiction">Fiction</option>
    </select>
    </div></div>
    <div class="column-50 p-l">
    <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Author</label>
    <input class="cu-form__input" type="text" name="author" required>
    </div>
    </div></div>
    <div style="padding:0px 0px;" class="row">
    <div class="column-50 p-r">
    <div class="login-page-new__main-form-row">
    <label  class="login-page-new__main-form-row-label">Language</label>
    <input class="cu-form__input" type="text" name="language"  required>
    </div>
    </div>
    <div class="column-50 p-l">
    <div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">No. of pages</label>
    <input  class="cu-form__input" type="number" name="pages"  required>
    </div>
</div>
</div>
<div style="padding:0px 0px;" class="row">  
    <span class="<?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
    
    <input class="" type="radio" name="type" value="rent" id="rent" onclick="javascript:BooktypeCheck();" required> 
    <label style="padding-right:20px;" >Rent</label>
</span>
    <span class="help-block"><?php echo $type_err; ?></span>
    <input class="" type="radio" name="type" value="sale" id="sale" onclick="javascript:BooktypeCheck();" required>
    <label >Sale</label>
</span>
    
</div>

<div class=" <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $price_err; ?></span>
<div id="for_sale">
<div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Price:</label>
    <input class="cu-form__input" type="text" name="price" id="price"  required>
    </div>   
</div></div>
<div class=" <?php echo (!empty($rate_err)) ? 'has-error' : ''; ?>">
<span class="help-block"><?php echo $rate_err; ?></span>
<div id="for_rent">
<div class="login-page-new__main-form-row">
    <label class="login-page-new__main-form-row-label">Monthly rate:</label>
    <input class="cu-form__input" type="text" id="rate" name="rate" required>
    </div>
 </div>     
</div>

<input class="login-page-new__main-form-button" type="submit" value="Upload">      
</form>
</div>
</div>   
</body>
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
<script>
$(document).ready(function() {
        $("#fileUpload").on('change', function() {
          //Get count of selected files
          var countFiles = $(this)[0].files.length;
          var imgPath = $(this)[0].value;
          var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
          var image_holder = $("#image-holder");
          image_holder.empty();
          if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof(FileReader) != "undefined") {
              //loop for each file selected for uploaded.
              for (var i = 0; i < countFiles; i++) 
              {
                var reader = new FileReader();
                reader.onload = function(e) {
                  $("<img />", {
                    "src": e.target.result,
                    "class": "thumb-image"
                  }).appendTo(image_holder);
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[i]);
              }
            } else {
              alert("This browser does not support FileReader.");
            }
          } else {
            alert("Pls select only images");
          }
        });
      });
</script>
</body>
</html>