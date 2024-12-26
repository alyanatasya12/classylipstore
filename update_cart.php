<?php
session_start();
include('mysqli.php');

// Ensure the user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Error: User not logged in.");
}

// Handle the POST request for "Update Cart" or "Remove" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Handle "Remove" action
    if ($_POST['action'] === 'remove') {
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
    }

    // Handle "Update All" action
    if ($_POST['action'] === 'update_all') {
        if (!empty($_POST['cart']) && is_array($_POST['cart'])) {
            foreach ($_POST['cart'] as $index => $item) {
                $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
                $shade_id = isset($item['shade_id']) ? intval($item['shade_id']) : 0;
                $quantity = isset($item['quantity']) ? max(1, intval($item['quantity'])) : 1;

                if ($product_id === 0 || $shade_id === 0) {
                    error_log("Invalid cart item: " . print_r($item, true));
                    continue; // Skip invalid items
                }

                // Update the cart item quantity in the database
                $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ? AND shade_id = ?");
                if (!$stmt) {
                    error_log("Failed to prepare UPDATE statement: " . $conn->error);
                    continue;
                }
                $stmt->bind_param("iiii", $quantity, $user_id, $product_id, $shade_id);
                if (!$stmt->execute()) {
                    error_log("Failed to execute UPDATE statement: " . $stmt->error);
                    continue;
                }
            }

            // Optionally, recalculate the total price here
            $total_price = 0;
            $result = $conn->query("SELECT SUM(cart_items.quantity * products.product_price) AS total
                                    FROM cart_items
                                    JOIN products ON cart_items.product_id = products.product_id
                                    WHERE cart_items.user_id = $user_id");
            if ($row = $result->fetch_assoc()) {
                $total_price = $row['total'];
            }

            // Store the updated total price in the session
            $_SESSION['total_price'] = $total_price;

            // Redirect to the cart page with an update status
            header("Location: ShoppingCart.php?status=updated");
            exit();
        }
    }

} else {
    die("Invalid request.");
}
