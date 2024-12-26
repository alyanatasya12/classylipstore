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
// Validate the order_id
if ($order_id <= 0) {
    die("Invalid order ID provided.");
}
// Query to fetch order details, items, and customer details
$query = "
    SELECT 
        o.order_date, 
        o.total_amount, 
        o.payment_method, 
        u.email, 
        IFNULL(ua.address, 'Unknown') AS address, 
        IFNULL(ua.city, 'Unknown') AS city, 
        IFNULL(ua.state, 'Unknown') AS state,
        IFNULL(ua.postcode, 'Unknown') AS postcode,
        IFNULL(ua.phone_number, 'Unknown') AS phone_number, 
        oi.quantity, 
        p.product_name, 
        s.shade_name, 
        p.product_price,
        pi.image_url
    FROM orders o
    INNER JOIN users u ON o.user_id = u.user_id
    LEFT JOIN useraddresses ua ON ua.user_id = u.user_id AND ua.is_default = 1  
    INNER JOIN order_items oi ON o.order_id = oi.order_id
    INNER JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_shades s ON oi.shade_id = s.shade_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    WHERE o.order_id = ? AND o.user_id = ?
    GROUP BY oi.order_item_id
";


$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Order query preparation failed: " . $conn->error);
}

$stmt->bind_param("ii", $order_id, $user_id);
if (!$stmt->execute()) {
    die("Failed to retrieve order details. Please try again later.");
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Invalid order ID or no permission to view this order.");
}

$order_items = [];
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}
$order_date = $order_items[0]['order_date'] ?? '';
$total_amount = $order_items[0]['total_amount'] ?? 0.00;
$payment_method = $order_items[0]['payment_method'] ?? 'Unknown';
$email = $order_items[0]['email'] ?? 'Unknown';
$address = $order_items[0]['address'] ?? 'Unknown';
$city = $order_items[0]['city'] ?? 'Unknown';
$state = $order_items[0]['state'] ?? 'Unknown';
$postcode = $order_items[0]['postcode'] ?? 'Unknown';
$phone_number = $order_items[0]['phone_number'] ?? 'Unknown';

?> 

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Confirmation</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700">
    <link rel="stylesheet" href="css/bootstrap-4.4.1.css">
    <link href="purchase.css" rel="stylesheet" type="text/css">
	<style>
	/* Button Styling */
.btn-outline {
  display: inline-block;
  padding: 10px 20px;
  font-size: 14px;
  color: #64341c;
  text-decoration: none;
  border: 1px solid #000;
  background-color: transparent;
  text-align: center;
  letter-spacing: 2px;
  font-family:Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif";




  transition: all 0.3s ease;
}

/* Hover Effect */
.btn-outline:hover {
  color: #64341c;
  background-color: #64341c;
}

	</style>
  </head>
  <body>
<div class="container4"><br>

    <div class="thank-you-message">
	        <h1 style="font-family: 'Sorts Mill Goudy', serif;    color: #A55548;  

">ClassyLip<h1>
        <h2>Thank You for Your Purchase!</h2>
        <p>We have received your order. Below are the details of your purchase.</p>
    </div>

    <div class="order-details">
        <?php foreach ($order_items as $item): ?>
            <div class="order-item-box">
                <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'images/default.jpg'); ?>" 
                     alt="Product Image" class="order-item-image">
                <div class="order-item-details">
                    <p><?php echo htmlspecialchars($item['product_name']); ?></p>
                    <p><strong>Shade:</strong> <?php echo htmlspecialchars($item['shade_name'] ?? 'N/A'); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p><strong>Price:</strong> RM<?php echo number_format($item['product_price'], 2); ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="order-summary">
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($order_date); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
            <p><strong>Subtotal:</strong> RM<?php echo number_format($total_amount , 2); ?></p>
            <p><strong>Shipping:</strong> RM10.00</p>
            <p><strong>Total:</strong> RM<?php echo number_format($total_amount + 10 , 2); ?></p>
        </div>
    </div>

    <div class="customer-details">
        <h3 style="font-weight: bold; font-size: 18px;">Order Details</h3>
        <p><?php echo htmlspecialchars($email); ?><br>
           <?php echo htmlspecialchars($address); ?><br>
           <?php echo htmlspecialchars("$city, $state, $postcode"); ?><br>
           <?php echo htmlspecialchars($phone_number); ?></p>
    </div>
	<div class="col-12">
            <p class="text-center">
                <a href="home.php" class="btn-outline" style=" color: black;">Continue Shopping → </a>
            </p>
        </div>
</div>


    <!-- jQuery and Bootstrap Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>
