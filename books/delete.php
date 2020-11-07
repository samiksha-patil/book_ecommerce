
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>

    <style type="text/css">
    body {
  font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif; 
}
</style>
</head>
<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script>
  

  
Swal.fire({
  title: 'Are you sure?',
  text: "You won't be able to revert this!",
  icon: 'warning',
  showCancelButton: true,
  
  confirmButtonText: 'Yes, delete it!'
}).then((result) => {
  if (result.isConfirmed) {
    window.location.href="del.php?id=<?php echo $_GET['id']; ?>"
    Swal.fire(
      'Deleted!',
      'Your book has been deleted.',
      'success'
    )
  }
  else{
    window.location.href="user_books.php"
  }
})
    
    
    </script>
</body>