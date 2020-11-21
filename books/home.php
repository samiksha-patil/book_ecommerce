<?php
// Include the database configuration file
include '../connection.php';
// echo "hello world";
// session_start();
// $id=$_SESSION["user_id"];

  include '../components/navbar.php'; 
?>
<link rel="stylesheet" href="../static/css/main.css">
<link rel="stylesheet" href="../static/css/styles.css">

<meta name="viewport" content="width=device-width, initial-scale=1">
<body>
<div class="container-top">
    <div class="main-img-top main-row" >
         <div class="main-column-50-main" >     
      <img class="top-book1" data-aos="fade-right" src="../static/images/h1-rev-img-01.png" alt="" />
      <img class="top-book2" data-aos="fade-right" src="../static/images/shop-img-08-600x525.png" alt="" />
      <img class="top-book3" data-aos="fade-right" src="../static/images/h1-rev-img-02.png" alt="" />
    </div>  
     
        <div style="margin:auto;" class="column-40" data-aos="fade-left">  
            
            <div class="title-main">This Month</div><br>
            <div class="elementor-divider-separator-left"></div>
                <p>Lorem ipsum dolor sit amet, consectetur adipis labore et dolore magna aliqua dolor.</p>
                <div class="explore-btn"><a href="list.php?q=&sort=newest&category=All">EXPLORE </a></div>
            
            </div>
    </div>

        </div>
</div>

<div class="main-row">
    <div style="padding: 2%;" class="main-column-50">
        
    <div  class="img-first"><div style="align-items: center;">
        <div class="head-sub"> Buy E Books Online</div>
        <div style="text-align: center;" class="explore-btn"><a href="list.php">EXPLORE </a></div>
    </div></div>

    </div>
    <div style="padding: 2%;" class="main-column-50">
    
    <div class="img-second"><div style="align-items: center;">
        <div style="font-size:40px;" class="head-sub"> Rent E Books Online</div>
        <div style="text-align: center;" class="explore-btn"><a href="list.php">EXPLORE </a></div>
    </div>
    </div>
</div>
</div>


<div class="product-title align-center" data-aos="zoom-in" >Newest Editions Out
<div class="elementor-divider-separator"></div>
<p style="margin: 15px; max-width: 300px; text-align: center;">At vero eos et accusamus et iusto od lorem ipsum gnissimos ducimus bland.</p>
</div>

<div class="parent">
    <?php
        $sql = "select * from book_view limit 4";
        // echo $sql;
        if($result = mysqli_query($link, $sql)){ 
            // echo mysqli_num_rows($result); 
            if(mysqli_num_rows($result) > 0){ 
                while($row = mysqli_fetch_array($result)){ ?>
        <div class="child filterDiv <?php echo $row['type'] ?>">
        <div class="container">
        <img style="height: 200px" src="../uploads/<?php echo $row["cover_image"]; ?>"
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
            <?php 
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
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
</script>
</body>
