<!DOCTYPE html>
<html lang="en">
<body>
<script>
function Update_status(){
    
  alert
}
</script>
<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}

$user_id= $_SESSION["user_id"];
require_once "../connection.php";


if(isset($_POST['reject'])){
$book_id = $_POST['id'];
$sql ="UPDATE queue set status='Cancelled' WHERE book_id=$book_id AND user_id=$user_id";

if(mysqli_query($link, $sql))
{
echo "The book requested has be succesfully cancelled";
}

}


$sql ="SELECT * from  queue  where status='pending'";
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){ ?>
        <div class="row books">
        <?php 
        $total =0;
        while($row = mysqli_fetch_array($result)){
?>
pendin requset
<form name="form" method="post">
<a href="payment_rent.php?id=<?php echo $row['book_id']  ?>" class="btn btn-info">accept</a> 
<input type="hidden" name="id" value="<?php echo $row['book_id'] ?>">
<input type="submit" name="reject" value="reject" />
<button onclick="Update_status()" class="btn btn-info">reject</button>
             <?php echo $row["book_id"]; 
                                                   
        }
    }
}
        

?>

<?php
#$sql ="UPDATE queue set status='Reject' WHERE book_id=$row['book_id'] AND user_id=$user_id";
?>
</body>