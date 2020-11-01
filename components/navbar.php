<?php 
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

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
          <a class="nav-link" href="#">Shop</a>
        </li>
        <li class="mobile-only">
          <a class="nav-link" href="#" style="padding-right: 3px"
            >Notifications</a
          >
          <span class="mobile-badge">5</span>
        </li>
        <li class="mobile-only">
          <a class="nav-link" href="#">Cart</a>
        </li>
        <li>
          <a class="nav-link" href="#">Orders</a>
        </li>
      </ul>
      <ul>
        <li>
          <div class="myDIV">
            <ion-icon name="notifications" id="notifIcon"></ion-icon>
            <span class="badge">5</span>
            <div id="notifContainer">

            <?php 
            $sql ="SELECT * from  notification_view  where status='Pending' and user_id=$user_id";
            if($result = mysqli_query($link, $sql)){
                if(mysqli_num_rows($result) > 0){ 
                    $total=0;
                    while($row = mysqli_fetch_array($result)){
                    ?>
                <div class="notif">
                <div>
                  <img
                    height="70"
                    src="../uploads/<?php echo $row["cover_image"] ?>"
                  />
                </div>
                <div>
                  <a class="book_title" href="#"><?php echo $row["title"] ?></a> is available for rent.
                  <br />
                  <div class="notif-date">Now</div>
                  <div class="notif-buttons">
                      <form name="form" method="post">
                    <a href="payment_rent.php?id=<?php echo $row['book_id']  ?>" class="primary">Rent Now</a> 
                    <input type="hidden" name="id" value="<?php echo $row['queue_id'] ?>">
                    <button type="submit" name="reject" class="secondary" />Cancel</button>
                  </div>
                </div>
              </div>
                    <?php
                    }
                } else {
                    ?>
                        No new notifications.
                    <?php
                }
            }
            ?>
            </div>
          </div>
        </li>
        <li>
          <ion-icon name="cart"></ion-icon>
        </li>
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
        <form action="/action_page.php">
          <input type="text" placeholder="Search.." name="search" />
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
