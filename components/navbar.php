<?php 

// function to convert timestamp 
// to time ago 
  
function to_time_ago( $time ) { 
      
    // Calculate difference between current 
    // time and given timestamp in seconds 
    $diff = time() - $time; 
      
    if( $diff < 60 ) {  
        return 'Now';  
    } 
      
    $time_rules = array (  
                12 * 30 * 24 * 60 * 60 => 'year', 
                30 * 24 * 60 * 60      => 'month', 
                24 * 60 * 60           => 'day', 
                60 * 60                => 'hour', 
                60                     => 'minute'
    ); 
  
    foreach( $time_rules as $secs => $str ) { 
          
        $div = $diff / $secs; 
  
        if( $div >= 1 ) { 
              
            $t = round( $div ); 
              
            return $t . ' ' . $str .  
                ( $t > 1 ? 's' : '' ) . ' ago'; 
        } 
    } 
} 

session_start();
$logged_in=True;
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    $logged_in=False;
}

if($logged_in)
  $user_id= $_SESSION["user_id"];
require_once "../connection.php";


if(isset($_POST['reject'])){
$queue_id = $_POST['id'];
$sql ="UPDATE queue set status='Cancelled' WHERE queue_id=$queue_id";
    if(mysqli_query($link, $sql))
    {
    // echo "The book requested has be succesfully cancelled";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../static/css/navbar.css" />

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    
    <link rel="stylesheet" href="../static/css/styles.css" />
    <link rel="stylesheet" href="../static/css/list.css" />
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Document</title>
  </head>
  <body>
    <nav>
      <div class="nav-title">
        <img
          class="logo"
          src="https://bridge315.qodeinteractive.com/wp-content/uploads/2020/01/logo-img-01.png"
        />
        <label for="show-menu" class="show-menu" id="menu-icon"
          ><ion-icon name="menu-outline"></ion-icon
        ></label>
      </div>
      <input type="checkbox" id="show-menu" role="button" />
      <ul id="menu">
        <li>
          <a class="nav-link" href="../books/list.php">Shop</a>
        </li>
        <?php 
          if($logged_in) {
        ?>
        <li class="mobile-only">
          <a class="nav-link" href="../order/notification.php" style="padding-right: 3px"
            >Notifications</a
          >
          <?php 
            $sql ="SELECT * from  notification_view  where status='Pending' and user_id=$user_id";
            if($result = mysqli_query($link, $sql)){
              if(mysqli_num_rows($result) > 0){?>
              <span class="mobile-badge"><?php echo mysqli_num_rows($result) ?></span>
              <?php }
            }
              ?>
        </li>
        <li class="mobile-only">
          <a class="nav-link" href="../order/cart.php">Cart</a>
        </li>
        <li>
          <a class="nav-link" href="../order/my_orders.php">Orders</a>
        </li>
        <?php }  else { ?>
        <li class="mobile-only">
          <a class="nav-link" href="../accounts/login.php">Login</a>
        </li>
        <li class="mobile-only">
          <a class="nav-link" href="../accounts/register.php">Register</a>
        </li>
          <?php } ?>
      </ul>
      <ul>
        <?php 
        if($logged_in) { ?>
        <li>
          <div class="myDIV">
            <ion-icon name="notifications" id="notifIcon"></ion-icon>
            <?php 
            $sql ="SELECT * from  notification_view  where status='Pending' and user_id=$user_id";
            if($result = mysqli_query($link, $sql)){
              if(mysqli_num_rows($result) > 0){?>
              <span class="badge"><?php echo mysqli_num_rows($result) ?></span>
              <?php }
              ?>
            <div id="notifContainer">

            <?php 
                if(mysqli_num_rows($result) > 0){ 
                    $total=0;
                    while($row = mysqli_fetch_array($result)){
                    ?>
                <div class="notif">
                <div style="margin: auto;">
                  <img
                    height="70"
                    src="../uploads/<?php echo $row["cover_image"] ?>"
                  />
                </div>
                <div>
                  <a class="book_title" href="#"><?php echo $row["title"] ?></a> is available for rent.
                  <br />
                  <div class="notif-date"><?php echo to_time_ago($row["unix_time"]) ?></div>
                  <div class="notif-buttons">
                      <form name="form" method="post">
                    <a href="../order/payment_rent.php?id=<?php echo $row['book_id']  ?>" class="primary">Rent Now</a> 
                    <input type="hidden" name="id" value="<?php echo $row['queue_id'] ?>">
                    <button type="submit" name="reject" class="secondary" />Cancel</button>
                  </div>
                </div>
              </div>
                    <?php
                    }
                } else {
                    ?>
                      <div class="no-notif">
                          No new notifications.
                      </div>
                    <?php
                }
            }
            ?>
            </div>
          </div>
        </li>
        <li>
          <a style="color: #000" href="../order/cart.php">
            <ion-icon name="cart"></ion-icon>
          </a>
        </li>
          <?php } else { ?>
        <li>
          <a class="nav-link" href="../accounts/login.php">Login</a>
        </li>
        <li>
          <a class="nav-link" href="../accounts/register.php">Register</a>
        </li>
          <?php } ?>
        <li>
          <ion-icon
            name="search"
            class="openBtn"
            onclick="openSearch()"
          ></ion-icon>
        </li>
      </ul>
    </nav>
    <div id="myOverlay" class="search-overlay">
      <span class="closebtn" onclick="closeSearch()" title="Close Overlay"
        >×</span
      >
      <div class="search-overlay-content">
        <form action="../books/list.php">
          <input type="text" placeholder="Search.." name="q" />
          <button>
            <ion-icon name="search"></ion-icon>
          </button>
        </form>
      </div>
    </div>

    <script>
      var notifContainer = document.getElementById("notifContainer");
      var notifIcon = document.getElementById("notifIcon");

      notifIcon.onclick = function () {
        notifContainer.style.display = "block";
      };
      window.onclick = function (event) {
        if (event.target != notifContainer && event.target != notifIcon) {
          notifContainer.style.display = "none";
        }
      };

      function openSearch() {
        document.getElementById("myOverlay").style.display = "block";
      }

      function closeSearch() {
        document.getElementById("myOverlay").style.display = "none";
      }
    </script>
  </body>
</html>
