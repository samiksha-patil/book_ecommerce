<?php
// Include the database configuration file
include '../connection.php';
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}
$id=$_SESSION["user_id"];

//  TODO
// refresh
// currently --> returned
// next in line: notification, waiting --> pending

if($_SERVER["REQUEST_METHOD"] == "GET"){
// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){    
    $param_id = trim($_GET["id"]);
    $type="";
    $available=true;
    $query = $link->query("SELECT * FROM book_view WHERE book_id = $param_id");
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="../../static/css/styles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<div class="container-top">
  <img
    class="img-top"
    src="../../static/images/title-img.jpg"
    alt="Cinque Terre"
    width="1000"
    height="300"
  />
  <div class="center product-title">
    Shop List
    <div class="elementor-divider-separator"></div>
  </div>
</div>

<!-- <div class="fg">
  <h1 class="top-title">Cart</h1>
</div> -->
<div class="row">
  <div style="text-align: center" class="column-50">
    <img class="img-book" src="../../uploads/<?php echo $row["cover_image"]; ?>" alt="" />
  </div>
  <div class="column-50">
    <div class="product-title"><?php echo $row["title"]; ?></div>
    <p style="font-size: 30px; line-height: 40px">$56</p>

    <p>
      <?php echo $row["info"]; ?>
    </p>
    <br />
        <?php 
        if($row["uploaded_by"]==$id) { ?>

        <a href='../update.php?id=<?php echo $row['book_id']; ?>'class='btn btn-info'>Update</a>                   
        <a href='../delete.php?id=<?php echo $row['book_id']; ?>'class='btn btn-danger'>Delete</a>
        <?php
        }
        else {
        if($row["type"]=="buy") {
            if($row["is_available"]==1) {
                $in_cart = false;
                $book_id = $row["book_id"];
                $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $id");
                if($query->num_rows > 0){  ?>
                <a href="../../order/cart.php" class="btn btn-primary">View in cart</a>
                <?php 
                } 
                else { ?>
                    <a href='../../order/add_to_cart.php?id=". $row['book_id'] ."'class='btn btn-info'>Add to Cart</a>
                <?php
                } 
            }
            else {
                ?>
                Sold Out
                <?php
            }
        } else {
            
                $query = $link->query("SELECT * FROM queue_view WHERE book_id = $param_id");
                if($query->num_rows > 0){ 
                    echo $query->num_rows."in line for this book<br>";
                    while($queue_row = mysqli_fetch_array($query)){
                        echo $queue_row["first_name"]." ".$queue_row["last_name"]." ".$queue_row["status"]."<br />";
                    }
                }
                $currently_renting = false;
                $book_id = $row["book_id"];
                $query = $link->query("SELECT status FROM queue_view WHERE book_id = $book_id AND user_id = $id limit 1");
                if($query->num_rows > 0){  
                    $status=$query->fetch_assoc()["status"];
                    ?>
                <button disabled><?php echo $status; ?></button>
                <?php 
                } 
                else {
                    if($row["is_available"]==1) { ?>
                    <form action="../queue.php" method="get">
                        <input type="hidden" name="id" value="<?php echo $param_id ?>">
                        <input type="submit" 
                        value="Rent">
                    </form>
                    <?php
                    }
                    else { ?>
                        <form action="../queue.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $param_id ?>">
                            <input type="submit" 
                            value="Join waiting list">
                        </form>
                    <?php
                    }
                } 
            }
        }
        ?>
    <div style="text-align: center">
      <button class="collapsible">DESCRIPTION</button>
      <div class="content">
        <p>
            <?php echo $row["info"]; ?>
        </p>
      </div>
      <button class="collapsible">ADDITIONAL INFORMATION</button>
      <div class="content">
        <p>
          <b>Category:</b> <?php echo $row["category"]; ?>
          <b>Author:</b> <?php echo $row["author"]; ?>
          <b>Language:</b> <?php echo $row["lang"]; ?>
          <b>No. of pages:</b> <?php echo $row["no_of_pages"]; ?>
        </p>
      </div>
      <button class="collapsible">People In Line</button>
      <div class="content">
        <p>
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
          eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
          minim veniam, quis nostrud exercitation ullamco laboris nisi ut
          aliquip ex ea commodo consequat.
        </p>
      </div>
    </div>
  </div>
</div>
<div class="product-title" style="font-size: 25px; margin-left: 5rem">
  Related products
</div>
<div class="parent">
  <div class="child">
    <div class="container">
      <img style="height: 200px" src="book.png" alt="" />
      <div class="overlay"></div>
      <div class="button"><a href="#"> ADD TO CART</a></div>
    </div>
    <div class="product-title" style="font-size: 22px; text-align: center">
      Most Popular Edition
    </div>
    <p style="text-align: center">$5</p>
  </div>
  <div class="child">
    <div class="container">
      <img style="height: 200px" src="book.png" alt="" />

      <div class="overlay"></div>
      <div class="button"><a href="#"> ADD TO CART </a></div>
    </div>
    <div class="product-title" style="font-size: 22px; text-align: center">
      Most Popular Edition
    </div>
    <p style="text-align: center">$5</p>
  </div>

  <div class="child">
    <div class="container">
      <img style="height: 200px" src="book.png" alt="" />

      <div class="overlay"></div>
      <div class="button"><a href="#">ADD TO CART </a></div>
    </div>
    <div class="product-title" style="font-size: 22px; text-align: center">
      Most Popular Edition
    </div>
    <p style="text-align: center">$5</p>
  </div>
  <div class="child">
    <div class="container">
      <img style="height: 200px" src="book.png" alt="" />

      <div class="overlay"></div>
      <div class="button"><a href="#"> ADD TO CART</a></div>
    </div>
    <div class="product-title" style="font-size: 22px; text-align: center">
      Most Popular Edition
    </div>
    <p style="text-align: center">$5</p>
  </div>
</div>

<script>
  var coll = document.getElementsByClassName("collapsible");
  var i;

  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function () {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      if (content.style.maxHeight) {
        content.style.maxHeight = null;
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
      }
    });
  }
</script>
</body>
</html>