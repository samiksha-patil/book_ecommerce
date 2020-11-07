<?php

    
    require_once "../connection.php";
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM book_view WHERE book_id=$book_id AND is_available=0";
    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result)==0){
    $sql = "DELETE FROM book WHERE book_id=$book_id";
    if(mysqli_query($link, $sql)){
        $code = 200;
        $text = 'OK';
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . $code . ' ' . $text);
        $GLOBALS['http_response_code'] = $code;
        echo 'SUCCESS';
        
        } else {
            echo mysqli_error($link);
        } 
    }
     else{
         
        $code = 500;
        $text = 'NOT OK';
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . $code . ' ' . $text);
        $GLOBALS['http_response_code'] = $code;
        echo 'Error';
            echo "Oops! Something went wrong. Please try again later.". mysqli_error($link);
        }
   
    mysqli_close($link);


?>
