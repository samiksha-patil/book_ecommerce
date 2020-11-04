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
                    <a href='../../order/add_to_cart.php?id=<?php echo $row['book_id'] ?>' class='btn btn-info'>Add to Cart</a>
                <?php
                } 
            }
            else {
                ?>
                Sold Out
                <?php
            }
        } else {
            
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
      <?php 
      if($row["type"]=="rent" && $row["is_available"]==0) {
        ?>

        <button class="collapsible">People In Line</button>
        <div class="content">
          <p>
          <?php
            $query = $link->query("SELECT * FROM queue_view WHERE book_id = $param_id");
                if($query->num_rows > 0){ 
                    echo $query->num_rows."in line for this book<br>";
                    while($queue_row = mysqli_fetch_array($query)){
                        echo $queue_row["first_name"]." ".$queue_row["last_name"]." ".$queue_row["status"]."<br />";
                    }
                }
            ?>
          </p>
        </div>

        <?php
      }
      ?>
      
    </div>
  </div>
</div>
<div class="product-title" style="font-size: 25px; margin-left: 5rem">
  Related products
</div>
<div class="parent">
    <?php
        $sql = "SELECT * FROM ((SELECT book.book_id, title, info, author, cover_image, monthly_rate AS price, 'rent' AS type, category FROM book INNER JOIN book_for_rent ON book.book_id=book_for_rent.book_id) UNION (SELECT book.book_id, title, info, author, cover_image, price, 'buy' AS type, category FROM book RIGHT JOIN book_for_sale ON book.book_id=book_for_sale.book_id)) as t limit 4";
        // echo $sql;
        if($result = mysqli_query($link, $sql)){ 
            // echo mysqli_num_rows($result); 
            if(mysqli_num_rows($result) > 0){ 
                while($row = mysqli_fetch_array($result)){ ?>
        <div class="child filterDiv <?php echo $row['type'] ?>">
        <div class="container">
        <img style="height: 200px" src="../../uploads/<?php echo $row["cover_image"]; ?>"
        alt="" />
        <div class="overlay"></div>
        <div class="button">
        <?php if($row['type']==="buy") {?>
        <a href="../order/add_to_cart.php?id=<?php echo $row['book_id']; ?>">Add to Cart</a>
        <?php } else { ?> 
        <a href="../books/queue.php?id=<?php echo $row['book_id']; ?>">Rent</a>
        <?php } ?>
        </div>
        </div>
        <div onclick="goToDetail(<?php echo $row["book_id"] ?>)">
        <div
            class="product-title"
            style="font-size: 22px; text-align: center"
        >
            <?php echo $row["title"]; ?>
        </div>
        <p style="text-align: center">
            Rs.
            <?php echo $row["price"] ?><?php if($row["type"] === "rent") echo "/month"; ?>
        </p>
        </div>
    </div>
                <?php }
                    
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

  function goToDetail(id) {
    window.location.href="../detail.php/?id="+id; 
  }
</script>
</body>
</html>