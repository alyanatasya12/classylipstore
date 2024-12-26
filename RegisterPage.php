<?php 
session_start();
include('mysqli.php');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classylip";

// Create a connection to the db
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("<script>alert('Connection failed: " . $conn->connect_error . "');</script>");
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form data
    $first_name = $conn->real_escape_string($_POST['firstName']);
    $last_name = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Check the email if already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        echo "<script>alert('An account with this email already exists. Please use another email.');</script>";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash) 
                VALUES ('$first_name', '$last_name', '$email', '$password_hash')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('New account created successfully!');
                window.location.href = 'LoginPage.php';
                </script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $sql . "\\n" . $conn->error . "');</script>";
        }
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create an Account</title>
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
        <input type="text" id="firstName" name="firstName" placeholder="First name" required> 
        <input type="text" id="lastName" name="lastName" placeholder="Last name" required>
        <input type="email" id="email" name="email" placeholder=" Email Address" required> 
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit">Create</button>
      </form>
      
      <div class="link-section">
        <p>Already have an account? <a href="LoginPage.php">Log In</a></p>
      </div>
      
      <div class="link-section">
        <p>Or <a href="home.php">Return to Store</a></p>
      </div>
    </div>
</div>
   <?php include 'includes/footer.php'; ?>
    <!-- jQuery and Bootstrap Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>
