<?php

// Define database connection constants
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', ''); 
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'classylip');

// Make the MySQL connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
