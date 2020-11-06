<?php
// Include connect file
require_once "../connection.php";
 
// Define variables and initialize with empty values
$email = $password = $confirm_password = $first_name = $last_name = $country= $state= $city=$phone_no="";
$email_err = $password_err = $confirm_password_err = $first_name_err = $last_name_err ="";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    } else{
        $sql = "SELECT user_id FROM customer WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    //validate firstname
    if(empty(trim($_POST["first_name"]))){
        $first_name_err = "Please enter your first name";     
    }
    else{
        $first_name = trim($_POST["first_name"]);
    }
    
    //validate lastname
    if(empty(trim($_POST["last_name"]))){
        $last_name_err = "Please enter your last name";     
    }
    else{
        $last_name = trim($_POST["last_name"]);
    }

    
    $country = $_POST["country"];
    $state = $_POST["state"];
    $city = $_POST["city"];
    $phone_no = $_POST["phone_no"];
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err))
    {
        $sql = "INSERT INTO customer (email, first_name, last_name, password, country, state, city) VALUES (?, ?, ?, ?, ?, ?, ?)";
        

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssssss", $param_email, $param_first_name, $param_last_name, $param_password, $param_country, $param_state, $param_city);
            
            $param_email = $email;
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_country = $country;
            $param_state = $state;
            $param_city = $city;
            $param_phone_no = $phone_no;

            if(mysqli_stmt_execute($stmt)){
                if(!empty($phone_no)){
                    // Get user_id of new user
                    $sql = "SELECT user_id FROM customer WHERE email=?";
                    
                    if($stmt1 = mysqli_prepare($link, $sql)){
                        mysqli_stmt_bind_param($stmt1, "s", $param_email);
                        
                        $param_email = $email;
                        
                        if(mysqli_stmt_execute($stmt1)){
                            mysqli_stmt_store_result($stmt1);
                            
                            // Check if username exists, if yes then verify password
                            if(mysqli_stmt_num_rows($stmt1) == 1){     
                                mysqli_stmt_bind_result($stmt1, $user_id);
                                if(mysqli_stmt_fetch($stmt1)){
                                    $param_user_id = $user_id;
                                    
                                    // Add phone numbers
                                    $sql = "INSERT INTO phone_no (phone_no,user_id) VALUES (?, ?)";
                                    if($stmt2 = mysqli_prepare($link, $sql)){
                                        mysqli_stmt_bind_param($stmt2, "sd", $param_phone_no, $param_user_id);
                            
                                        if(mysqli_stmt_execute($stmt2)){
                                            // Redirect to login page
                                            header("location: login.php");
                                        } else{
                                            echo "Oops! Something went wrong. Please try again later.";
                                        }
                                        mysqli_stmt_close($stmt2);            
                                    }
                                }
                            }
                            mysqli_stmt_close($stmt1);
                        } else{
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                }

            } else{
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);

}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../static/css/form.css">
<link rel="stylesheet" href="../static/css/styles.css">
</head>

<body>
<?php 
    include '../components/navbar.php'; 
    ?>
   
<div class="registration-container">
  <div class="login-row">
  <div class="signup-col-1">
      <div class="reg-bg">
          <div class="image-content">
              <div class="">Already have an Account? Login</div>
          </div>
      </div>
 
  </div>
  <div>


        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div style="padding: 30px 75px 0;" >
        <div class="login-page-new__main-form-title">Sign Up</div>
            
<div style="padding:0px 0px;" class="row">
<div class="column-50 p-r">
        <div  class="login-page-new__main-form-row">
        
            <div class=" <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label class="login-page-new__main-form-row-label">First Name</label>
                <input required type="text" class="cu-form__input ng-pristine ng-invalid ng-touched" name="first_name"  value="<?php echo $first_name; ?>">
                <span class="help-block"><?php echo $first_name_err; ?></span>
            </div> 
        </div>
</div>
<div class="column-50 p-l">
<div class="login-page-new__main-form-row">
       
            <div class="<?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label class="login-page-new__main-form-row-label">Last Name</label>
                <input required class="cu-form__input ng-pristine ng-invalid ng-touched" type="text" name="last_name"  value="<?php echo $last_name; ?>">
                <span class="help-block"><?php echo $last_name_err; ?></span>
            </div>  
            </div>      
</div>   
</div>

        <div class="login-page-new__main-form-row">
            <div class="<?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
            <div class="login-page-new__main-form-row-label">Email</div>
                <input required type="email" class="cu-form__input ng-pristine ng-invalid ng-touched" name="email" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
        </div>

<div style="padding:0px 0px;" class="row">
<div class="column-50 p-r">
        <div class="login-page-new__main-form-row">
            <div class="<?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label class="login-page-new__main-form-row-label" >Password</label>
                <input required class="cu-form__input ng-pristine ng-invalid ng-touched" type="password" name="password" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
        </div>
</div>
<div class="column-50 p-l">
        <div class="login-page-new__main-form-row">
            <div class="<?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label class="login-page-new__main-form-row-label" >Confirm Password</label>
                <input required class="cu-form__input ng-pristine ng-invalid ng-touched" type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
        </div>
    </div>
</div>
    <div style="padding:0px 0px;" class="row">
<div class="column-50 p-r">
            <div class="login-page-new__main-form-row ">
            
                <label class="login-page-new__main-form-row-label">Country</label>
                <input class="cu-form__input ng-pristine ng-invalid ng-touched" type="text" name="country" value="<?php echo $country; ?>">
                </span>
            </div>
</div>       
<div class="column-50 p-l">
            <div class="login-page-new__main-form-row ">
                <label class="login-page-new__main-form-row-label">State</label>
                <input class="cu-form__input ng-pristine ng-invalid ng-touched" type="text" name="state"  value="<?php echo $state; ?>">
                </span>
</div></div></div>
<div style="padding:0px 0px;" class="row">
<div class="column-50 p-r">
            <div class="login-page-new__main-form-row">
            
                <label class="login-page-new__main-form-row-label">City</label>
                <input class="cu-form__input ng-pristine ng-invalid ng-touched" type="text" name="city"  value="<?php echo $city; ?>">
                </span>
            
</div></div>
<div class="column-50 p-l">
<div class="login-page-new__main-form-row">
            
                <label class="login-page-new__main-form-row-label">Phone Number</label>
                <input class="cu-form__input ng-pristine ng-invalid ng-touched" type="text" name="phone_no" value="<?php echo $phone_no; ?>">
                </span>
</div>      </div>   </div>

                <input class="login-page-new__main-form-button" type="submit" value="Submit">
              
         
           
       
        </form>
</div>
</div>
</div>   
</body>
</html>


















<!-- <div class="container">
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
</div> -->