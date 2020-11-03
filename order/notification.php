
<?php

include '../components/navbar.php'; 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

$sql ="SELECT * from  notification_view  where status='Pending' and user_id=$user_id";
if($result = mysqli_query($link, $sql)){
    ?>
    <div style="background: #f6f1ee; min-height: 100vh; ">
    <div style="
    text-align: center;
    font-family: 'Crimson Text';
    font-weight: 400;
    font-size: 30px; 
    padding-top: 40px;">Notifications</div>
<div id="notifContainerMobile">

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
        <div class="notif-date">Now</div>
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
    ?>
    </div>
    <?php
}
?>