<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Book uploaded as rent</h2>
                        <a href="book_form.php" class="btn btn-success pull-right">sell or rent a book</a>
                    </div>
                    <?php
                    include 'Config.php';
                    session_start();
                    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                        header("location: login.php");
                        exit;
                    }
                    $id=$_SESSION["user_id"];
                    $sql = "SELECT * FROM book INNER JOIN book_for_rent on book.book_id=book_for_rent.book_id WHERE user_id=$id";
                   
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Title</th>";
                                        echo "<th>Cover</th>";
                                        echo "<th>Author</th>";
                                        echo "<th>Description</th>";
                                        echo "<th>Language</th>";
                                        echo "<th>No. of pages</th>";
                                        echo "<th>Monthly Rent</th>";
                                        echo "<th>Rating</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    $imageURL = 'uploads/'.$row["cover_image"];
                                    echo "<tr>";
                                        echo "<td>" . $row['title'] . "</td>";
                                        echo "<td><img style=height:150px; src='$imageURL' /></td>";                                       
                                        echo "<td>" . $row['author'] . "</td>";
                                        echo "<td>" . $row['info'] . "</td>";
                                        echo "<td>" . $row['lang'] . "</td>";
                                        echo "<td>" . $row['no_of_pages'] . "</td>";
                                        echo "<td>" . $row['monthly_rate'] . "</td>";
                                        echo "<td>" . $row['rating'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='read.php?id=". $row['book_id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='update.php?id=". $row['book_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='delete.php?id=". $row['book_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";

                    
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                   
                    ?>
                </div>
            </div>        
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Books for sell</h2>
                       
                    </div>
                    <?php
                   
                    $sql = "SELECT * FROM book RIGHT JOIN book_for_sale on book.book_id=book_for_sale.book_id WHERE user_id=$id";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                    echo "<th>Title</th>";
                                    echo "<th>Cover</th>";
                                    echo "<th>Author</th>";
                                    echo "<th>Description</th>";
                                    echo "<th>Language</th>";
                                    echo "<th>No. of pages</th>";
                                    echo "<th>Price</th>";
                                    echo "<th>Discount Price</th>";
                                    echo "<th>Actions</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    $imageURL = 'uploads/'.$row["cover_image"];
                                    echo "<tr>";
                                    echo "<td>" . $row['title'] . "</td>";
                                    echo "<td><img style=height:150px; src='$imageURL' /></td>";                                       
                                    echo "<td>" . $row['author'] . "</td>";
                                    echo "<td>" . $row['info'] . "</td>";
                                    echo "<td>" . $row['lang'] . "</td>";
                                    echo "<td>" . $row['no_of_pages'] . "</td>";
                                    echo "<td>" . $row['price'] . "</td>";
                                    echo "<td>" . $row['discount_price'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='read.php?id=". $row['book_id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='update.php?id=". $row['book_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='delete.php?id=". $row['book_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";

                    
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>


</body>
</html>