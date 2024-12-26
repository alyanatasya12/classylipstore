<?php 
session_start();
include('mysqli.php');

// Ensure the user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Error: User not logged in.");
}

// Handle "Remove" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $shade_id = isset($_POST['shade_id']) ? intval($_POST['shade_id']) : 0;

    if ($product_id === 0 || $shade_id === 0) {
        error_log("Invalid remove request: " . print_r($_POST, true));
        die("Invalid data.");
    }

    // Remove the item from the cart_items table
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND shade_id = ?");
    if ($stmt) {
        $stmt->bind_param("iii", $user_id, $product_id, $shade_id);
        if ($stmt->execute()) {
            // Redirect back to the shopping cart page
            header("Location: ShoppingCart.php?status=removed");
            exit();
        } else {
            error_log("Failed to execute DELETE statement: " . $stmt->error);
            die("Failed to remove item.");
        }
    } else {
        error_log("Failed to prepare DELETE statement: " . $conn->error);
        die("Database error.");
    }
} else {
    die("Invalid request.");
}
?>

