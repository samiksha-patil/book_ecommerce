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
<style>

.swal2-styled.swal2-confirm {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #ea524d !important;
    color: #fff;
    font-size: 1.0625em;
}
.swal2-styled.swal2-cancel {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #8f8d9abd !important;
    color: #fff;
    font-size: 1.0625em;
}
.swal2-styled:focus {
    outline: 0 !important;
    box-shadow: none !important;
}
</style>

</head>
<body>
<div class="container-top">
    <img class="img-top" src="../static/images/title-img.jpg" alt="Cinque Terre" width="1000" height="300">
    <div style="font-size:60px;" class="center product-title">Dashboard<div class="elementor-divider-separator"></div></div>
  
</div>
<div style="float:right; margin:30px 30px 0px 0px;" class="explore-btn"><a href="create.php">ADD A BOOK</a></div> 
<br><br>
<div style="padding:30px 55px 0px;">
    <span class="head-book">Books added by you for Rent</span>
    </div>                    
                    </div>
                    <?php
                    include '../connection.php';
                    session_start();
                    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                        header("location: ../accounts/login.php");
                        exit;
                    }
                    $id=$_SESSION["user_id"];
                    $sql = "SELECT * FROM book_view  WHERE uploaded_by=$id AND type='rent'";
                   
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
?>


                            <div class="card card-custom ">
   

    <div class="card-body">
     
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center" >
                <thead>
                    <tr class="text-left">
                   
                        <th class="pr-0" style="width: 150px"></th>
                        <th style="min-width: 200px">Title</th>                        
                        <th style="min-width: 150px">Monthly Rate</th>
                        <th style="min-width: 150px">Category</th>
                        <th style="min-width: 150px">Status</th>
                        <th style="min-width: 150px; max-width:150px;">No. of  Times Rented</th>
                        <th class="pr-0 text-right" style="min-width: 150px">action</th>
                    </tr>
                </thead>
                <tbody>
                            <?php
                                while($row = mysqli_fetch_array($result)){

?>



                                    <tr>
                        
                                    <td class="pr-0">
                                        <div class="symbol symbol-50 symbol-light mt-1">
                                            <span class="symbol-label">
                                                <img src="../uploads/<?php echo $row["cover_image"]; ?>" class="h-75 align-self-end" alt="">
                                            </span>
                                        </div>
                                    </td>
                                    <td class="pl-0">
                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $row['title'];?></span>
                                        <span class="text-muted d-block">by <?php echo $row['author'];?></span>
                                    </td>
                                    
                                    <td>
                                        
                                        <span >$<?php echo $row['price'] ; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column w-100 mr-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class=" mr-2 font-size-sm"><?php echo $row['category']; ?></span>
                                               
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column w-100 mr-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <?php if($row['is_available']) {?>
                                                <span class="bt-status-avaliable">Avaliable</span>
                                                <?php } 
                                                else { ?>

                                                <span class="bt-status-sold-out">Rented</span>
                                          

                                                <?php } ?>
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td>
                                        
                                        <span >
                                        <?php
                                                $book_id=$row['book_id'];
                                       $query1 = $link->query("SELECT * from queue where status='Currently renting' or status='Returned' and book_id=$book_id");
                                           if($query1->num_rows > 0){ 
                                               echo $query1->num_rows;
                                            
                                           }else{
                                               echo 0;
                                           }
            ?>

                                        </span>
                                    </td>
                                    <td class="pr-0 text-right">
                                   
                                    <a style="color: #6993FF;font-size: 24px;" class="svg-icon svg-icon-md svg-icon-primary" href='detail.php/?id=<?php echo $row['book_id'];?>' title='View Record' data-toggle='tooltip'>
                                    <ion-icon name="eye"></ion-icon>
                                        </a>

                                        <a href='update.php?id=<?php echo $row['book_id'];?>' title='Update Record' data-toggle='tooltip'>
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <!--begin::Svg Icon | path:/metronic/theme/html/demo2/dist/assets/media/svg/icons/Communication/Write.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)"></path>
                                                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </a>
                                        <span onclick='goTo(<?php echo $row["book_id"] ?>)'>
                                        
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <!--begin::Svg Icon | path:/metronic/theme/html/demo2/dist/assets/media/svg/icons/General/Trash.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                                                        <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </span>
                                       
                                    </td>
                                </tr>



                              








<?php
                                }?>

                                  
                </tbody>
                </table>
            </div>
            <!--end::Table-->
        </div>
        <!--end::Body-->
                            </div>
                    <?php
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='align-center no-record'><em> No records were found.</em></p>";
                        }
                        
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                   
                    ?>
                </div>
            </div>        
        </div>
        <div style="padding:30px 55px 0px;">
    <span class="head-book">Books added by you for Sale</span>
    </div>                    
           
                    <?php
                   
                    $sql = "SELECT * FROM book_view  WHERE uploaded_by=$id AND type='buy'";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                        ?>   
                            <div class="card card-custom ">
   

    <div class="card-body">
     
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center" >
                <thead>
                    <tr class="text-left">
                   
                        <th class="pr-0" style="width: 100px"></th>
                        <th style="min-width: 200px">Title</th>    
                        <th style="min-width: 150px">Price</th>
                        
                        <th style="min-width: 150px">Category</th>
                        <th style="min-width: 150px">Status</th>
                        <th class="pr-0 text-right" style="min-width: 150px">action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                                while($row = mysqli_fetch_array($result)){
                    ?>
                                    <tr>
                        
                                    <td class="pr-0">
                                        <div class="symbol symbol-50 symbol-light mt-1">
                                            <span class="symbol-label">
                                                <img src="../uploads/<?php echo $row["cover_image"]; ?>" class="h-75 align-self-end" alt="">
                                            </span>
                                        </div>
                                    </td>
                                    <td class="pl-0">
                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $row['title'];?></span>
                                        <span class="text-muted d-block">by <?php echo $row['author'];?></span>
                                    </td>
                                    
                                    <td>
                                        
                                       
                                        <?php
                                                $book_id=$row['book_id'];
                                       $query1 = $link->query("SELECT discount_price from book_for_sale  where book_id=$book_id");
                                       $row1 = $query1->fetch_assoc();
                                           if($query1->num_rows >0){ 
                                               if($row1['discount_price']==null || $row1['discount_price']==0 ){
                                                echo '$'.$row['price'] ;
                                               }
                                               else{
                                                   ?>
                                                <span style="padding-right:10px;" class="text-muted" ><del>$<?php echo $row['price'] ; ?></del></span>
                                                <?php
                                               echo '$'.$row1['discount_price'];
                                               }
                                           }
            ?>


                                    </td>
                                    <td>
                                        <div class="d-flex flex-column w-100 mr-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class=" mr-2 font-size-sm"><?php echo $row['category']; ?></span>
                                               
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column w-100 mr-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <?php if($row['is_available']) {?>
                                                <span class="bt-status-avaliable">Avaliable</span>
                                                <?php } 
                                                else { ?>

                                                <span class="bt-status-sold-out">Sold Out</span>
                                                <?php } ?>
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td class="pr-0 text-right">
                                    <a style="color: #6993FF;font-size: 24px;" class="svg-icon svg-icon-md svg-icon-primary" href='detail.php/?id=<?php echo $row['book_id'];?>' title='View Record' data-toggle='tooltip'>
                                    <ion-icon name="eye"></ion-icon>
                                        </a>
                                        <a href='update.php?id=<?php echo $row['book_id'];?>' title='Update Record' data-toggle='tooltip'>
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <!--begin::Svg Icon | path:/metronic/theme/html/demo2/dist/assets/media/svg/icons/Communication/Write.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)"></path>
                                                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </a>
                                        <span onclick='goTo(<?php echo $row["book_id"] ?>)'>
                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                <!--begin::Svg Icon | path:/metronic/theme/html/demo2/dist/assets/media/svg/icons/General/Trash.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                                                        <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                                </span>
                                    </td>
                                </tr>





                                    <?php
                                }
                                ?>
                                                  
                </tbody>
                </table>
            </div>
            <!--end::Table-->
        </div>
        <!--end::Body-->
                            </div>
<?php
                    
                            // Free result set
                            mysqli_free_result($result);
                        }
                        
                        else{
                            echo "<p class='align-center no-record'><em>No records were found.</em></p>";
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

        <script>


function goTo(id) {
       
 Swal.fire({
  title: 'Are you sure?',
  text: "You won't be able to revert this!",
  icon: 'warning',
  showCancelButton: true,
  
  confirmButtonText: 'Yes, delete it!'
}).then((result) => {
  if (result.isConfirmed) {

    window.location.href="del.php?id="+id;
    Swal.fire(
      'Deleted!',
      'Your book has been deleted.',
      'success'
    )
    

  }
})
   

}
</script>
</body>

</html>
