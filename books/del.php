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
