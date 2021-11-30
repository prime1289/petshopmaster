<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
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
?>
 

<html>
<head>
<title>
	petshop
</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  margin: 0;

    background-size: cover;
  background: #484848;
   background: -webkit-linear-gradient(right, #484848,#1a1a1a);
  background: -moz-linear-gradient(right, #1a1a1a,  #484848);
  background: -o-linear-gradient(right,#484848,#1a1a1a);
  background: linear-gradient(to left, #1a1a1a, #484848);
  font-family: "Roboto", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale; 
  /*background-color:rgba(43, 3, 3, 0.945);*/
  
}
.topnav {
  overflow: hidden;
  background-color:rgb(73, 25, 21);
  height: 70px;
  border: 2px solid black;
}

.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 35px;
  font-weight: bold;
}
.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 8px 8px 0  #491915;
  border: 5px solid  #491915;
  border-radius: 3px; 
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background:rgba(249, 105, 14, 1);
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
  
}
.form button:hover,.form button:active,.form button:focus {
  background: rgba(249, 105, 14, 1);
}
	

</style>
</head>
<body>
    <div class="topnav">
            <a class="active" href="login.php"><img src="ic_add_pet.png"></a>
            <a href="login.php">pets shop</a>
          </div>
    <div class="login-page">
  <div class="form">
    <!-- <form class="login-form" action="index.php" method="POST">
      <h1>Login</h1>
      <input type="text" name="t1" placeholder="username" required/>
      <input type="password"  name="t2" placeholder="password" required/>
    </form> -->
	<form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
			<label>Username</label>
			<input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
			<span class="help-block"><?php echo $username_err; ?></span>
		</div>    
		<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
			<label>Password</label>
			<input type="password" name="password" class="form-control">
			<span class="help-block"><?php echo $password_err; ?></span>
		</div>
		<button type="submit"  name="t23" >login</button>
		<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
	</form>
  </div>
</div>
        

     
       
</body>	

</html>