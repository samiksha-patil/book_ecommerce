<?php

include '../connection.php';
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../accounts/login.php");
    exit;
}
$id=$_SESSION["user_id"];
$sql_condition="";
$q_query="";
$q_category="";
if(isset($_GET["category"]) && !empty(trim($_GET["category"]))) 
    if($_GET["category"] == "All")
        $_GET["category"]="";
$q_sort="";
$query="%";
if(isset($_GET["q"]) && !empty(trim($_GET["q"]))){    
    $q_query = trim($_GET["q"]);
    $query="%".$q_query."%";
    $sql_condition .= " WHERE (title LIKE '$query' OR info LIKE '$query' OR author LIKE '$query')";
} 
if(isset($_GET["category"]) && !empty(trim($_GET["category"]))){    
    $q_category = trim($_GET["category"]);
    if(isset($_GET["q"]) && !empty(trim($_GET["q"]))) {
        $sql_condition .= " AND " ;
    }
    else {
        $sql_condition .= " WHERE ";
    }
    $sql_condition .= "category = '$q_category'";
} 
if(isset($_GET["sort"]) && !empty(trim($_GET["sort"]))){    
    $q_sort = trim($_GET["sort"]);
    $sql_condition .= " ORDER BY ";
    if($q_sort=="a-z") $sql_condition .= "title";
    else if($q_sort=="z-a") $sql_condition .= "title DESC";
    else if($q_sort=="newest") $sql_condition .= "book.book_id DESC";
    else if($q_sort=="oldest") $sql_condition .= "book.book_id";
} 
?>
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
        .books img {
            height: 200px
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
        <div class="row">
        <div class="col-md-3">
            <div class="container-fluid">
                <h3>Filters</h3>
                <form>
                    <input type="text" id="search" name="q" value="<?php echo $q_query ?>" placeholder="Search something..."></input>
                    <br>
                    <!-- Price Range
                    <br>
                    <input type="nu mber" id="lowest_price" value="<?php echo $q_lowest ?>"></input>-
                    <input type="number" id="highest_price" value="<?php echo $q_highest ?>"></input>
                    <br> -->
                    Sort by
                    <select id="sort" name="sort">
                        <option value="low-high">$-$$$</option>
                        <option value="high-low">$$$-$</option>
                        <option value="a-z">A-Z</option>
                        <option value="z-a">Z-A</option>
                        <option value="newest">Newest</option>
                        <option value="oldest">Oldest</option>
                    </select>
                    <br>
                    Category
                    <br>
                    <input type="radio" id="Textbook" name="category" value="Textbook">
                    <label for="Textbook">Textbook</label>
                    <br>
                    <input type="radio" id="Memoir" name="category" value="Memoir">
                    <label for="Memoir">Memoir</label>
                    <br>
                    <input type="radio" id="Essays" name="category" value="Essays">
                    <label for="Essays">Essays</label>
                    <br>
                    <input type="radio" id="Action and Adventure" name="category" value="Action and Adventure">
                    <label for="Action and Adventure">Action and Adventure</label>
                    <br>
                    <input type="radio" id="Classics" name="category" value="Classics">
                    <label for="Classics">Classics</label>
                    <br>
                    <input type="radio" id="Comic Book or Graphic Novel" name="category" value="Comic Book or Graphic Novel">
                    <label for="Comic Book or Graphic Novel">Comic Book or Graphic Novel</label>
                    <br>
                    <input type="radio" id="Fiction" name="category" value="Fiction">
                    <label for="Fiction">Fiction</label>
                    <br>
                    <input type="radio" id="All" name="category" value="All">
                    <label for="All">All</label>
                    <br>
                    <button type="submit">Apply</button>
                </form>
            </div>
        </div>
        <div class="col-md-9">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $sql = "SELECT * FROM book INNER JOIN book_for_rent on book.book_id=book_for_rent.book_id".$sql_condition;
                    if($q_sort == "low-high")   $sql .= "monthly_rate";
                    else if($q_sort == "high-low")   $sql .= "monthly_rate DESC";
                    // echo $sql;
                    if($result = mysqli_query($link, $sql)){ ?>
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Books for rent <?php echo mysqli_num_rows($result); ?> </h2>
                    </div>
                        <?php if(mysqli_num_rows($result) > 0){ ?>
                        <div class="row books">
                        <?php while($row = mysqli_fetch_array($result)){ ?>
                                    <div class="col-sm-3">
                                    <a href="detail.php?id=<?php echo $row["book_id"] ?>">
                                        <div class="card" style="width: 18rem;">
                                            <img class="card-img-top" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="Card image cap" >
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $row["title"]; ?></h5>
                                                <p class="card-text">Rs. <?php echo $row["monthly_rate"] ?>/month</p>
                                                <a href="detail.php?id=<?php echo $row["book_id"] ?>" class="btn btn-primary">View</a>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                <?php } ?>
                    </div>
                    <?php
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
                    ?>
                </div>
            </div>        
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $sql = "SELECT * FROM book RIGHT JOIN book_for_sale on book.book_id=book_for_sale.book_id".$sql_condition;
                    if($q_sort == "low-high")   $sql .= "price";
                    else if($q_sort == "high-low")   $sql .= "price DESC";
                    // echo $sql;
                    if($result = mysqli_query($link, $sql)){?>
                        <div class="page-header clearfix">
                            <h2 class="pull-left">Books for sale <?php echo mysqli_num_rows($result); ?> </h2>
                        </div>
                            <?php if(mysqli_num_rows($result) > 0){ ?>
                            <div class="row books">
                            <?php while($row = mysqli_fetch_array($result)){ ?>
                                        <div class="col-sm-3">
                                        <a href="detail.php?id=<?php echo $row["book_id"] ?>">
                                            <div class="card" style="width: 18rem;">
                                                <img class="card-img-top" src="../uploads/<?php echo $row["cover_image"]; ?>" alt="Card image cap" >
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $row["title"]; ?></h5>
                                                    <p class="card-text">Rs. <?php echo $row["price"] ?></p>
                                                    </a>
                                                    <a href="detail.php?id=<?php echo $row["book_id"] ?>" class="btn btn-primary">View</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                        </div>
                        <?php
                    
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
        </div>
        </div>
    <script>
    document.getElementById("sort").value ='<?php echo $q_sort ?>';
    var value="<?php echo $q_category; ?>";
    if(value=="") value="All";
    value.replaceAll('+',' ');
    console.log(value);
    $("input[name=category][value='" + value + "']").prop('checked', true);
    </script>
</body>
</html>