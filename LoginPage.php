<?php 
session_start();
include('mysqli.php');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classylip";

$conn = new mysqli($servername, $username, $password, $dbname);
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
	
    // Query the database for the entered email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the entered password with the stored hashed password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role']; // Store role in session
            
            // Check if user is an admin
            if ($user['role'] === 'admin') {
                // Redirect to the admin dashboard
                header("Location: admin/pendingorders.php");
            } else {
                // Redirect to the homepage or user dashboard
                header("Location: home.php");
            }
            exit();
        } else {
            // Password is incorrect
            echo "<script>alert('The password you entered is incorrect. Please try again.');</script>";
        }
    } else {
        // No user found with that email
        echo "<script>alert('No account found with that email. Please check and try again.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700"> 
    <link rel="stylesheet" href="fonts/icomoon/style.css">  
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/bootstrap-4.4.1.css" rel="stylesheet">
    <link href="login.css" rel="stylesheet" type="text/css">
  </head>
  
  <body>
  <div class="site-wrap">
           <?php include 'includes/navigation.php'; ?>
    <div class="container4">
<img src="images/logonew.png" alt="Logo" class="login-logo">
      <form action="#" method="POST">
        <input type="email" id="email" name="email" placeholder="Enter your email" required> 
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <button type="submit">Login</button>
      </form>
      
      <div class="link-section">
        <p>Don't have an account? <a href="RegisterPage.php">Create Account</a></p>
      </div>
	  </div>
      <div class="site-wrap">
   <?php include 'includes/footer.php'; ?>

    <!-- jQuery and Bootstrap Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
	
  </body>
</html>
