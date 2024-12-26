<?php 
session_start();
include('mysqli.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_price = 0;

// Fetch cart items
$stmt = $conn->prepare("
    SELECT 
        ci.product_id,
        ci.shade_id,
        ci.quantity,
        p.product_price
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.product_id
    WHERE ci.user_id = ?
");

if (!$stmt) {
    die("Cart items query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $item_total = $row['product_price'] * $row['quantity'];
        $total_price += $item_total;

        $cart_items[] = [
            'product_id' => $row['product_id'],
            'shade_id' => $row['shade_id'],
            'quantity' => $row['quantity'],
            'item_total' => $item_total, // Save item total for each row
        ];
    }
} else {
    die("Failed to fetch cart items: " . $stmt->error);
}

if (empty($cart_items)) {
    die("Your cart is empty. Please add items to your cart before checkout.");
}

// Check if `payment_method` is set
if (!isset($_POST['payment-method']) || empty($_POST['payment-method'])) {
    die("Payment method not provided.");
}

$payment_method = $_POST['payment-method'];

// Start a transaction
$conn->begin_transaction();

try {
    // Insert into `orders` table (one order per checkout)
    $order_date = date("Y-m-d H:i:s");
    $order_status = "Pending"; // Default status

    $stmt = $conn->prepare("
        INSERT INTO `orders` 
        (user_id, order_date, payment_method, order_status, total_amount)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Order query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("isssd", $user_id, $order_date, $payment_method, $order_status, $total_price);

    if (!$stmt->execute()) {
        throw new Exception("Order execution failed: " . $stmt->error);
    }

    $order_id = $stmt->insert_id; // Get the inserted order_id

    // Insert each cart item into `order_items` table
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("
            INSERT INTO `order_items` 
            (order_id, product_id, shade_id, quantity)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Order items query preparation failed: " . $conn->error);
        }

        $stmt->bind_param("iiii", $order_id, $item['product_id'], $item['shade_id'], $item['quantity']);

        if (!$stmt->execute()) {
            throw new Exception("Order items execution failed: " . $stmt->error);
        }
    }

    // Clear the cart after placing the order
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Cart clear query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to clear cart: " . $stmt->error);
    }

    // Commit the transaction
    $conn->commit();

    // Redirect to the order summary page
    header("Location: purchase.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
?>
