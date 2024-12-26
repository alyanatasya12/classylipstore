<?php
session_start();
include 'mysqli.php'; // Database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php"); // Redirect to login page if not logged in
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;

// Query to fetch orders for the user
$query = "SELECT o.order_id, o.order_date, o.order_status, u.first_name, u.last_name, o.total_amount
          FROM orders o
          INNER JOIN users u ON o.user_id = u.user_id
          WHERE o.user_id = ?";

if ($start_date) {
    $query .= " AND o.order_date >= ?"; 
}

$stmt = $conn->prepare($query);

// Bind parameters: user_id as an integer, start_date as a string if provided
if ($start_date) {
    // If start_date is set, bind both user_id (integer) and start_date (string)
    $stmt->bind_param('is', $user_id, $start_date);
} else {
    // If no start_date is set, bind only user_id (integer)
    $stmt->bind_param('i', $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-4.4.1.css">
    <link href="orderhistory1.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/navigation.php'); ?>   

    <!-- Main Content -->
    <div class="container">
        <h3><b>Order History</b></h3>

        <!-- Date Search Form -->
        <form method="post" action="">
            <div class="form-group">
                <label for="start-date">Search Date:</label>
                <input type="date" id="start-date" name="start_date" class="form-control" style="width: 200px; font-size: 12px; height: 30px; display: inline-block;">
                <button type="submit" class="print-btn" style="margin-left: 10px;">Search</button>
            </div>
        </form>

        <!-- Order Table -->
        <div class="order-table">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr>
                        <th style="border-bottom: 2px solid #ccc; padding: 10px;">Order ID</th>
                        <th style="border-bottom: 2px solid #ccc; padding: 10px;">Order Date</th>
                        <th style="border-bottom: 2px solid #ccc; padding: 10px;">Bill To Name</th>
                        <th style="border-bottom: 2px solid #ccc; padding: 10px;">Total</th>
                        
                        <th style="border-bottom: 2px solid #ccc; padding: 10px;">Products</th>
						 <th style="border-bottom: 2px solid #ccc; padding: 10px;">Status</th>
						<th style="border-bottom: 2px solid #ccc; padding: 10px;">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td >{$row['order_id']}</td>";
                            echo "<td>{$row['order_date']}</td>";
                            echo "<td >{$row['first_name']} {$row['last_name']}</td>";
                            echo "<td>RM{$row['total_amount']}</td>";
                           


                            // Query to fetch products in this order
                            $order_id = $row['order_id'];
                            $product_query = "SELECT p.product_name, ps.shade_name, oi.quantity
                                              FROM order_items oi
                                              INNER JOIN products p ON oi.product_id = p.product_id
                                              INNER JOIN product_shades ps ON oi.shade_id = ps.shade_id
                                              WHERE oi.order_id = ?";

                            $product_stmt = $conn->prepare($product_query);
                            $product_stmt->bind_param('i', $order_id);
                            $product_stmt->execute();
                            $product_result = $product_stmt->get_result();

                            // Display the products in this order
                            echo "<td style='padding: 10px;'>";
                            while ($product = $product_result->fetch_assoc()) {
                                echo "<p>{$product['product_name']} - {$product['shade_name']} (x{$product['quantity']})</p>";
                            }
                            echo "</td>";
							  echo "<td>{$row['order_status']}</td>";
							echo "<td style='padding: 10px;'>
                                 <a href='purchase.php?order_id={$row['order_id']}' class='print-btn'>View</a>
                                      </td>";
                             
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding: 10px;'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>
