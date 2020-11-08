
<?php
include "../components/navbar.php";

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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<style>

.swal2-styled.swal2-confirm {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #ff7962fc !important;
    color: #fff;
    font-size: 1.0625em;
}
.swal2-styled.swal2-cancel {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #8f8d9abd !important;
    color: #fff;
    font-size: 1.0625em;
}
.swal2-styled:focus {
    outline: 0 !important;
    box-shadow: none !important;
}
</style>
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
    <div class="title-row">
      <div class="product-title"><?php echo $row["title"]; ?></div>
      <div class="price" style="font-size: 20px; line-height: 40px"><span class="amount"><?php 
       if($row['discount_price']==null || $row['discount_price']==0 ){
          echo '$'.$row['price'] ;
          }
          else{
              ?>
         <span style="text-decoration: line-through; font-size: 20px; line-height: 40px" class="price">$<?php echo $row['price'] ; ?></span>
          <?php
          echo '$'.$row['discount_price'];
          }
      ?></span><?php if($row["type"] === "rent"){ ?>/month<?php } ?></div>
    </div>

    <p>
      <?php echo $row["info"]; ?>
    </p>
    <br />
      
        <?php 
        if($row["uploaded_by"]==$user_id) { ?>

        <div class="buttons-row">
        <button id="update_button" onclick="goto('../update.php?id=<?php echo $row['book_id']; ?>')" class='btn'>Update</button>                   
        <button onclick="deleteBook(<?php echo $row['book_id'];?>)" class='btn btn-red'>Delete</button>
       
       </div>
        <?php
        }
        else {
        if($row["type"]=="buy") {
            if($row["is_available"]==1) {
                $in_cart = false;
                $book_id = $row["book_id"];
                $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $user_id");
                if($query->num_rows > 0){  ?>
                <a href="../../order/cart.php" class="btn">View in cart</a>
                <?php 
                } 
                else { ?>
                    <a href='../../order/add_to_cart.php?id=<?php echo $row['book_id'] ?>' class='btn'>Add to Cart</a>
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
                $query = $link->query("SELECT status FROM queue_view WHERE book_id = $book_id AND user_id = $user_id limit 1");
                if($query->num_rows > 0){  
                    $status=$query->fetch_assoc()["status"];
                    ?>
                <button class="btn btn-disable" disabled><?php echo $status; ?></button>
                <?php 
                } 
                else {
                    if($row["is_available"]==1) { ?>
                    <form action="../queue.php" method="get">
                        <input type="hidden" name="id" value="<?php echo $param_id ?>">
                        <input type="submit" class="btn"
                        value="Rent">
                    </form>
                    <?php
                    }
                    else { 
                        ?>
                        <form action="../queue.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $param_id ?>">
                            <input type="submit" class="btn"
                            value="Join waiting list">
                        </form>
                    <?php
                    }
                } 
            }
        }
        ?>
      <button class="collapsible">ADDITIONAL INFORMATION</button>
      <div class="content">
        <div class="info-cols">
          <div class="info-row">
            <div class="key"><b>Category</b></div>
            <div class="value"><?php echo $row["category"]; ?></div>
          </div>
          <div class="info-row">
            <div class="key"><b>Author</b></div>
            <div class="value"><?php echo $row["author"]; ?></div>
          </div>
          <div class="info-row">
            <div class="key"><b>Language</b></div>
            <div class="value"><?php echo $row["lang"]; ?></div>
          </div>
          <div class="info-row">
            <div class="key"><b>No. of pages</b></div>
            <div class="value"><?php echo $row["no_of_pages"]; ?></div>
          </div>
        </div>
        </p>
      </div>
      
      <?php
      $query = $link->query("SELECT * FROM queue_view WHERE book_id = $param_id");
        if(($row["type"]=="rent") && ($query->num_rows > 0)) { ?>
      <button class="collapsible">WAITING LIST
        <span class="list-badge"><?php echo $query->num_rows ?></span>
      </button>
      <div class="content">
        <?php 
                if($query->num_rows > 0){ 
                    while($queue_row = mysqli_fetch_array($query)){
                      ?>
                      <table style="width:100%; max-width: 800px;">      
                        <tbody>
                          <tr>
                            <th>Rented by</th> <th>Status</th>
                          </tr>
                          <tr>
                            <td><?php echo $queue_row["first_name"]." ".$queue_row["last_name"]; ?></td> <td><?php echo $queue_row["status"] ?></td>
                          </tr>
                        </tbody>
                      </table>
                        <?php
                    }
                }
        ?>
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
        $sql = "select * from book_view where book_id<>$book_id limit 4";
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
        <p style="text-align: center"><?php 
              if($row['discount_price']==null || $row['discount_price']==0 ){
                  echo '$'.$row['price'] ;
                  }
                  else{
                      ?>
                <span style="text-decoration: line-through; font-size: 20px; line-height: 40px">$<?php echo $row['price'] ; ?></span>
                  <?php
                  echo '$'.$row['discount_price'];
                  }
              ?><?php if($row["type"] === "rent") echo "/month"; ?>
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

  function goto(link) {
    window.location.href=link; 
  }

  function deleteBook(id) {
       
       Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
      
          window.location.href="../del.php?id="+id;
        
          
      
        }
      })
         
      
      } 
</script>
</body>
</html>