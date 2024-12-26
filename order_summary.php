<?php
session_start();
include('mysqli.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php");
    exit();
}

$order_id = intval($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Debugging: Log inputs for debugging
error_log("Order ID from URL: " . $order_id);
error_log("User ID from session: " . $user_id);

// Validate the order_id
if ($order_id <= 0) {
    die("Invalid order ID provided.");
}

// Query to fetch order details and items
$query = "
    SELECT 
        o.order_date, 
        o.total_amount, 
        o.payment_method, 
        oi.quantity, 
        p.product_name, 
        s.shade_name, 
        p.product_price
    FROM orders o
    INNER JOIN order_items oi ON o.order_id = oi.order_id
    INNER JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_shades s ON oi.shade_id = s.shade_id
    WHERE o.order_id = ? AND o.user_id = ?
";

$stmt = $conn->prepare($query);

// Handle preparation failure
if (!$stmt) {
    error_log("SQL Error: " . $conn->error);
    die("Order query preparation failed: " . $conn->error);
}

// Bind parameters to the query
$stmt->bind_param("ii", $order_id, $user_id);

// Execute the query
if (!$stmt->execute()) {
    error_log("Order query execution failed: " . $stmt->error);
    die("Failed to retrieve order details. Please try again later.");
}

// Fetch results
$result = $stmt->get_result();

// Check if the order exists
if ($result->num_rows === 0) {
    error_log("No rows returned for order_id=$order_id and user_id=$user_id");
    die("Invalid order ID or no permission to view this order.");
}

// Fetch and organize order details
$order_details = [];
while ($row = $result->fetch_assoc()) {
    $order_details[] = $row;
}

// Extract overall order information
$order_date = $order_details[0]['order_date'] ?? '';
$total_amount = $order_details[0]['total_amount'] ?? 0.00;
$payment_method = $order_details[0]['payment_method'] ?? 'Unknown';

// Debugging: Log extracted details
error_log("Order Date: " . $order_date);
error_log("Total Amount: " . $total_amount);
error_log("Payment Method: " . $payment_method);


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="ordersummary.css" rel="stylesheet" type="text/css">
    <title>Order Summary</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .order-summary-container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .thank-you-message {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        .thank-you-message h1 {
            font-size: 2.5em;
            color: #333;
            margin: 0;
        }
        .thank-you-message p {
            color: #666;
            font-size: 1.1em;
        }
        .order-details {
            margin-top: 20px;
        }
        .order-details h2 {
            font-size: 1.8em;
            color: #444;
            margin-bottom: 10px;
        }
        .order-info {
            margin-bottom: 20px;
            font-size: 1em;
            color: #555;
        }
        .order-info p {
            margin: 5px 0;
        }
        .order-items {
            border-top: 1px solid #eaeaea;
            padding-top: 10px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }
        .order-item-info {
            flex: 1;
            margin-left: 15px;
        }
        .order-item-info p {
            margin: 0;
            font-size: 1em;
            color: #555;
        }
        .order-item-info p span {
            color: #888;
        }
        .order-item-price {
            font-size: 1.1em;
            color: #333;
        }
        .total-summary {
            text-align: right;
            margin-top: 20px;
        }
        .total-summary p {
            font-size: 1.2em;
            margin: 5px 0;
        }
        .total-summary .total {
            font-weight: bold;
            font-size: 1.5em;
        }
        .back-to-home {
            display: block;
            text-align: center;
            margin-top: 30px;
        }
        .back-to-home .btn {
    background-color: white;
    color: #00000; 
    text-align: center;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    border: 2px solid #00000;
    display: inline-block;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.back-to-home .btn:hover {
    background-color: #00000; 
    color: #000000; 
    transform: scale(1.05);
}

    </style>
</head>
<body>

  <div class="order-summary-container">
        <div class="thank-you-message">
            <h1>Thank You for Your Purchase!</h1>
            <p>Your order has been placed successfully.</p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="order-info">
                <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_date); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
            </div>

            <div class="order-items">
                <h2>Items Purchased</h2>
                <?php foreach ($order_details as $item): ?>
                    <div class="order-item">
                        <div class="order-item-details">
                            <p><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></p>
                            <p>Shade: <?php echo htmlspecialchars($item['shade_name'] ?? 'N/A'); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p>Price: MYR<?php echo number_format($item['product_price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-summary">
                <p><strong>Subtotal:</strong> MYR<?php echo number_format($total_amount - 10, 2); ?></p>
                <p><strong>Shipping:</strong> MYR10.00</p>
                <p class="total"><strong>Total:</strong> MYR<?php echo number_format($total_amount, 2); ?></p>
            </div>
        </div>

        <div class="back-to-home">
            <a href="home.php" class="btn">Continue Shopping</a>
        </div>
    </div>
</div>
</div>

</body>
</html>
