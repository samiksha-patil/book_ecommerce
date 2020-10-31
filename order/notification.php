<!DOCTYPE html>
<html lang="en">
<body>
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
    echo "The book requested has be succesfully cancelled";
    }
}


$sql ="SELECT * from  notification_view  where status='Pending' and user_id=$user_id";
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){ ?>
        <div class="row books">
        <?php 
        $total=0;
        while($row = mysqli_fetch_array($result)){
        ?>
        <h3>
        <?php echo $row["title"] ?>
        </h3> is now available for rent.
        <form name="form" method="post">
        <a href="payment_rent.php?id=<?php echo $row['book_id']  ?>" class="btn btn-info">Rent Now</a> 
        <input type="hidden" name="id" value="<?php echo $row['queue_id'] ?>">
        <input type="submit" name="reject" value="Cancel" />
        <?php
        }
    }
}
?>

</body>