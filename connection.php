<?php
    // Include config.php
    include 'config.php';

    /* Attempt to connect to MySQL database */
    $link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . $link->connect_error);
    }
?>