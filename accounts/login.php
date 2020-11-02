<?php
// Initialize the session
// session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../welcome.php");
    exit;
}
 
// Include connect file
require_once "../connection.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT user_id, email, password FROM customer WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $user_id, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $user_id;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirect user to welcome page
                            header("location: ../welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}

// include "../components/navbar.php";


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../static/css/form.css">

</head>
<body>
    <?php 
    include '../components/navbar.php'; 
    ?>
   
<div class="container">
  <div class="row">
    <div class="col-40">
  <img style="height:400px" src="../static/images/book-login.jpg">
</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

<div style="padding: 30px 60px 26px;" class="col-60">
  <div class="login-page-new__main-form-title">Welcome back!</div>
    <div class="login-page-new__main-form-row">

    <div class=" <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">

        <div class="login-page-new__main-form-row-label">Email</div>
        <div class="login-page-new__main-form-row-icon">
          <svg fill="none" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M10.121.878a3 3 0 00-4.242 0L.877 4.88A2.989 2.989 0 000 7v7a2 2 0 002 2h12a2 2 0 002-2V7a2.99 2.99 0 00-.879-2.122l-5-4zm3.042 4.986L8 8.844l-5.164-2.98 4.457-3.565a1 1 0 011.414 0l4.456 3.565zm.838 1.825l-5.49 3.17a.993.993 0 01-1.012.007L2 7.69V14h12V7.69z" fill="#B9BEC7" fill-rule="evenodd"></path>
          </svg>
        </div>
        <input type="email" name="email"  value="<?php echo $email; ?>" class="cu-form__input ng-pristine ng-invalid ng-touched" formcontrolname="email" id="login-email-input" placeholder="Enter your email" >
        <span class="help-block" ><?php echo $email_err; ?></span>
    </div>
    </div>   
     
    


    <div class="login-page-new__main-form-row">
    <div class=" <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">

        <div class="login-page-new__main-form-row-label">Password</div>
        <div class="login-page-new__main-form-row-icon">
          <svg fill="none" viewBox="0 0 14 16" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M5 4a2 2 0 114 0v2.058A35.706 35.706 0 007 6c-.695 0-1.37.022-2 .058V4zM3 6.22V4a4 4 0 118 0v2.22a39.5 39.5 0 011.315.162C13.306 6.52 14 7.373 14 8.336v4.962c0 .836-.529 1.624-1.39 1.873C11.554 15.478 9.407 16 7 16s-4.553-.522-5.61-.829A1.938 1.938 0 010 13.298V8.336c0-.963.693-1.815 1.685-1.954.35-.05.796-.106 1.315-.161zM2 8.358v4.909c.983.282 2.896.734 5 .734s4.017-.452 5-.734V8.357A37.569 37.569 0 007 8c-2.075 0-3.961.213-5 .357zM7 9a1 1 0 011 1v2a1 1 0 11-2 0v-2a1 1 0 011-1z" fill="#B9BEC7" fill-rule="evenodd"></path>
          </svg>
        </div>
        <input type="password" name="password"  autocomplete="off" autocorrect="off" class="cu-form__input cu-form__input-pwd ng-untouched ng-pristine ng-invalid" formcontrolname="password" maxlength="100" placeholder="Enter password" spellcheck="false" >
        <span class="help-block"><?php echo $password_err; ?></span>
    </div>
      </div>

      <input type="submit" value="LOG IN" class="login-page-new__main-form-button" type="submit">

 
  <p class="sub-register">Don't have an account? <a href="register.php">Sign up now</a></p>
  </form>
</div> 
</div>
</div>
</body>
</html>




