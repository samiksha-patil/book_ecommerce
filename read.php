<?php
// Include the database configuration file
include 'connection.php';

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){    

    $param_id = trim($_GET["id"]);
    // Get images from the database
    $query = $link->query("SELECT * FROM book WHERE book_id = $param_id");
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
    }
    else {
        header("location: error.php");
        exit();
    }
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1><?php echo $row["title"]; ?></h1>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <p class="form-control-static"><?php echo $row["info"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <p class="form-control-static"><?php echo $row["author"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Lanuage</label>
                        <p class="form-control-static"><?php echo $row["lang"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>No. of pages</label>
                        <p class="form-control-static"><?php echo $row["no_of_pages"]; ?></p>
                    </div>
                    <img src="uploads/<?php echo $row["cover_image"]; ?>">
                    <p><a href="user_books.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>