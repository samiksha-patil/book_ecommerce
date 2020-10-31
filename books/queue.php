<?php
// Include the database configuration file
include '../connection.php';
echo "hello world";
session_start();
$id=$_SESSION["user_id"];
    
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(isset($_POST["id"]) && !empty($_POST["id"]))
    {
        $book_id = $_POST['id'];
        echo $book_id, $id;
        // $no_of_months = $_POST["no_of_months"];
        // $query1 = $link->query("SELECT ADDDATE(CURDATE(),INTERVAL $no_of_months MONTH ) AS date_return ");
        // if($query->num_rows == 1){
        // $row = $query1->fetch_assoc();
        // $date_of_return = $row["date_return"];
        // }
        // echo $date_of_return;

        $query = $link->query("SELECT user_id FROM queue WHERE book_id=$book_id AND (status='Currently Renting' OR status='Waiting') AND user_id=$id");
        if($query->num_rows == 0){
            $query = $link->query("SELECT is_available FROM book_for_rent WHERE book_id=$book_id");
            $row = $query->fetch_assoc();
            if($row["is_available"]==1){
                $query = $link->query("INSERT INTO queue (user_id, book_id, status, date_of_request) VALUES ('$id','$book_id','Pending',NOW())");
                header("location: ../order/payment_rent.php?id=$book_id");
                exit;
            }
            else {
                $query = $link->query("INSERT INTO queue (user_id, book_id, status, date_of_request) VALUES ('$id','$book_id','Waiting',NOW())");
            }
        }
        else{
            echo "You've already rented/in queue for this book.";
            exit();
        }
        mysqli_close($link);
        header("location: detail.php?id=$book_id");
    }
    else{
        if(empty(trim($_POST["id"]))){
            header("location: ../error.php");
            exit();
        }
    }   
}

?>