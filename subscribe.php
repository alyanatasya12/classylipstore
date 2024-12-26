<?php
// Include the database connection file
require_once 'mysqli.php'; // Replace with your actual connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); // Get the email from the form

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address!'); window.history.back();</script>";
        exit;
    }

    // Prepare and execute the SQL statement to save the email
    $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for subscribing!'); window.location.href = 'home.php';</script>"; 
    } else {
        if ($stmt->errno == 1062) {
            echo "<script>alert('This email is already subscribed!'); window.history.back();</script>";
        } else {
            echo "<script>alert('An error occurred. Please try again later.'); window.history.back();</script>";
        }
    }

    $stmt->close();
}
$conn->close();
?>
