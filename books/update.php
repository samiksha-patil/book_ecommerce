<?php

include "../components/navbar.php";

$title_err = $type_err=$price_err=$rate_err="";
$type="";
$updated=false;

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
        header("location: /book_ecommerce/books/");
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
            $discount_price = $_POST['discount_price'];
            $price =$_POST['price'];
            $sql1 = "UPDATE book_for_sale SET price=$price, discount_price=$discount_price WHERE book_id=$param_id";
            if(mysqli_query($link, $sql1))
                $updated=true;
        } 
        if($type=="rent") {
            $rate = $_POST['rate'];
            $sql1 = "UPDATE book_for_rent SET monthly_rate=$rate WHERE book_id=$param_id";
            if(mysqli_query($link, $sql1))
                $updated=true;
        }
        else {
            // header("location: ../error.php");
        }
        // header("location: user_books.php");
        // $query -> free_result();
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
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../static/css/form.css">
<link rel="stylesheet" href="../static/css/styles.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
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
  cursor:pointer;
  width: fit-content;
  margin: auto;
}
</style>
</head>
<body>
<div class="registration-container" >
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data" id="update_form">

  <div class="login-row" >

  <div style="margin:auto;">

   <div >   
    <div  class="abc  login-page-new__main-form-row-label" for="cover_image">Update Cover Image <ion-icon name="cloud-upload-outline"></ion-icon>

    <input  id="fileUpload"
     class="hide_file" type="file"  name="file">
    </div>
    </div>
    <div style="margin:auto;"> 
      <div  id="image-holder"><img class="thumb-image" src="../uploads/<?php echo $row["cover_image"]; ?>"  > </div>
</div></div>
  <div class="">


  <div style="padding: 30px 60px 0px 26px ;" >
        <div class="login-page-new__main-form-title">Update the Book</div>
            <div class="login-page-new__main-form-row">
                <div class="login-page-new__main-form-row">    
                    <input type="hidden" name="id" value="<?php echo $param_id; ?>"/>
                        <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                        <label class="login-page-new__main-form-row-label">Title</label>
                        <input class="cu-form__input" type="text" name="title"  required value="<?php echo $row["title"]; ?>">
                        <span class="help-block"><?php echo $title_err; ?></span>
                        </div></div>
                        <div class="login-page-new__main-form-row">
                        <label class="login-page-new__main-form-row-label" >Description:</label>
                        <textarea class="cu-form__input" name="info" form="update_form" required><?php echo $row["info"]; ?></textarea>
</div>
                        <div style="padding:0px 0px;" class="row">
                        <div class="column-50 p-r">
                        <div class="login-page-new__main-form-row">
                        <label class="login-page-new__main-form-row-label" for="category">Category:</label>
                        <select  id="category" name="category">
                        <option value="Textbook">Textbook</option>
                        <option value="Memoir">Memoir</option>
                        <option value="Essays">Essays</option>
                        <option value="Action and Adventure">Action and Adventure</option>
                        <option value="Classics">Classics</option>
                        <option value="Comic Book or Graphic Novel">Comic Book or Graphic Novel</option>
                        <option value="Fiction">Fiction</option>
                        </select></div>
                        </div><div class="column-50 p-l">
                        <div class="login-page-new__main-form-row">
                        <label class="login-page-new__main-form-row-label">Author:</label>
                        <input class="cu-form__input" type="text" name="author" required value="<?php echo $row["author"]; ?>">
                        </div></div> </div>
                        <div style="padding:0px 0px;" class="row">
                        <div class="column-50 p-r">
                        <div class="login-page-new__main-form-row">
                        <label class="login-page-new__main-form-row-label">Language:</label>
                        <input class="cu-form__input" type="text" name="language"  required value="<?php echo $row["lang"]; ?>">
                        </div>
                        </div><div class="column-50 p-l">
                        <div class="login-page-new__main-form-row">
                                                <label class="login-page-new__main-form-row-label">No. of pages:</label>
                                                <input class="cu-form__input" type="number" name="pages"  required value="<?php echo $row["no_of_pages"]; ?>">
                        </div></div> </div>
                        <?php if($type=="sale") { ?>
                        <div class=" <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                        <span class="help-block"><?php echo $price_err; ?></span>
                        <div id="for_sale">
                            <label class="login-page-new__main-form-row-label">Price</label>
                            <input class="cu-form__input" type="text" name="price" id="price"  required value="<?php echo $sale_row["price"]; ?>">
                            <label>Discount Price:</label>
                            <input type="text" name="discount_price" required value="<?php echo $sale_row["discount_price"]; ?>">
                            </div>   
                        </div>
                        <?php } else { ?>
                        <div class=" <?php echo (!empty($rate_err)) ? 'has-error' : ''; ?>">
                        <span class="help-block"><?php echo $rate_err; ?></span>
                        <div id="for_rent">
                            <label class="login-page-new__main-form-row-label">Monthly rate</label>
                            <input class="cu-form__input" type="text" id="rate" name="rate"  required value="<?php echo $rent_row["monthly_rate"]; ?>">
                        </div>
                        <?php } ?>
                        
                        </div>
                        </div>
                        <div style="padding:0px 0px;" class="row">
                        
                        <input class="login-page-new__main-form-button" type="submit" name="submit" value="Update" id="submit"> 
                        
                    <a style="background-color: #7b7c7d; text-decoration:none;" class="login-page-new__main-form-button" href="user_books.php" >Back</a>
                    </div>
                    </div>
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
<script>
    <?php 
    if($updated==true) {
        ?>
            window.location.href="user_books.php";
        <?php
    }
    ?>
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