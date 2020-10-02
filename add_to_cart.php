

<?php

if(isset($_GET["id"]) && !empty($_GET["id"]))
{
    require_once "connection.php";
    $book_id = $_GET['id'];
    
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: user_books.php");
        exit;
    }
    $id=$_SESSION["user_id"];
    
    $query = $link->query("SELECT cart_item_id FROM cart_item WHERE book_id = $book_id AND user_id = $id");
    if($query->num_rows == 0){
        
    $sql = "INSERT INTO cart_item (book_id, user_id) VALUES ('$book_id','$id')";
    if(mysqli_query($link, $sql)){
        
        //echo "Ooong. Please try again later.";
        header("location: read.php?id=$book_id");
        
        } 
     else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    else{
        echo "Book already exists in your cart";
    }
    mysqli_close($link);
}
    else{
    
        if(empty(trim($_GET["id"]))){
            header("location: error.php");
            exit();
        }


    }

?>

