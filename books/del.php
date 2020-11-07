<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
        <link rel="stylesheet" href="../static/css/main.css">
    <link rel="stylesheet" href="../static/css/styles.css">
    <link rel="stylesheet" href="../static/css/user-books.css">
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

     <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>


<?php

    
    require_once "../connection.php";
    $book_id = $_GET['id'];
    $sql = "DELETE FROM book WHERE book_id=$book_id";
    
    if(mysqli_query($link, $sql)){
        
            
            header("location: user_books.php");
        
        } 
     else{
         
        
            echo "Oops! Something went wrong. Please try again later.". mysqli_error($link);
        }
   
    mysqli_close($link);


?>
