<?php
// Include connect file
require_once "connection.php";
 
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
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>">
                <span class="help-block"><?php echo $first_name_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>">
                <span class="help-block"><?php echo $last_name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            Address
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="<?php echo $country; ?>">
                </span>
            </div> 
            <div class="form-group">
                <label>State</label>
                <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
                </span>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo $city; ?>">
                </span>
            </div> 
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_no" class="form-control" value="<?php echo $phone_no; ?>">
                </span>
            </div> 
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>