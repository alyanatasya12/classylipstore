<?php  
session_start();
include('mysqli.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php?redirect=checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;

// Fetch user details 
$stmt = $conn->prepare("
    SELECT first_name, last_name, email 
    FROM users 
    WHERE user_id = ?
");
if (!$stmt) {
    die("Failed to prepare query for user details: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No user found for user_id: " . $user_id);
}

$user_info = $result->fetch_assoc();

// Fetch the default address 
$stmt = $conn->prepare("
    SELECT 
        address, 
        city, 
        state, 
        postcode, 
        phone_number 
    FROM useraddresses 
    WHERE user_id = ? 
    AND is_default = 1
");
if (!$stmt) {
    die("Failed to prepare query for default address: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $user_address = null; 
} else {
    $user_address = $result->fetch_assoc();
}

// Handle new address submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_billing'])) { 
        
        if (isset($_POST['address'], $_POST['city'], $_POST['state'], $_POST['postcode'], $_POST['phone'])) {
            $address = trim($_POST['address']);
            $city = trim($_POST['city']);
            $state = trim($_POST['state']);
            $postcode = trim($_POST['postcode']);
            $phone_number = trim($_POST['phone']);

            // Validate input data
            if (empty($address) || empty($city) || empty($state) || empty($postcode) || empty($phone_number)) {
                die("All fields are required.");
            }

            
            $address = $conn->real_escape_string($address);
            $city = $conn->real_escape_string($city);
            $state = $conn->real_escape_string($state);
            $postcode = $conn->real_escape_string($postcode);
            $phone_number = $conn->real_escape_string($phone_number);

            // Insert the new address into the database
            $stmt = $conn->prepare("
                INSERT INTO useraddresses 
                (user_id, address, city, state, postcode, phone_number, is_default) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) {
                die("Failed to prepare INSERT statement: " . $conn->error);
            }

            $is_default = ($user_address === null) ? 1 : 0; 
            $stmt->bind_param("isssssi", $user_id, $address, $city, $state, $postcode, $phone_number, $is_default);
            if (!$stmt->execute()) {
                die("Failed to save address: " . $stmt->error);
            }

            // fetch the new address 
            $stmt = $conn->prepare("
                SELECT 
                    address, 
                    city, 
                    state, 
                    postcode, 
                    phone_number 
                FROM useraddresses 
                WHERE user_id = ? 
                AND is_default = 1
            ");
            if (!$stmt) {
                die("Failed to prepare query for default address: " . $conn->error);
            }

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $user_address = null; // No default address
            } else {
                $user_address = $result->fetch_assoc();
            }

            
            header("Location: checkout.php"); 
            exit(); 
        }
    }

    if (isset($_POST['place_order'])) { 
       
    }
}

// Fetch cart items
$stmt = $conn->prepare("
    SELECT 
        ci.product_id,
        ci.shade_id,
        ci.quantity,
        p.product_name,
        s.shade_name,
        p.product_price,
        pi.image_url
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.product_id
    INNER JOIN product_shades s ON ci.shade_id = s.shade_id
    LEFT JOIN product_images pi ON s.shade_id = pi.shade_id AND pi.image_order = 1
    WHERE ci.user_id = ?
");
if (!$stmt) {
    die("Failed to prepare query for cart items: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $item_total = $row['product_price'] * $row['quantity'];
        $total_price += $item_total;

        $cart_items[] = [
            'product_name' => $row['product_name'],
            'shade_name' => $row['shade_name'],
            'product_price' => $row['product_price'],
            'image_url' => $row['image_url'] ?: 'default-image.jpg',
            'quantity' => $row['quantity'],
            'item_total' => $item_total,
        ];
    }
} else {
    die("Failed to fetch cart items: " . $conn->error);
}

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >Check Out</title>
 <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700"> 
    <link rel="stylesheet" href="fonts/icomoon/style.css">
	  	  	<link rel="stylesheet"
 		 href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
	  
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap -->
      <link href="css/bootstrap-4.4.1.css" rel="stylesheet">
	    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
  <link href="checkoutpage.css" rel="stylesheet" type="text/css">

</head>

    <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

<main class="checkout-container">
    <!-- Billing Form -->
    <form class="billing-form" method="POST" action="checkout.php">
        <h2 style="  font-family: 'Sorts Mill Goudy', serif;  ">Contact</h2>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>

        <h2 style="  font-family: 'Sorts Mill Goudy', serif;  ">Billing Details</h2>
        <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($user_info['first_name'] ?? ''); ?>" required>
        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($user_info['last_name'] ?? ''); ?>" required>
        <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($user_address['address'] ?? ''); ?>" required>
        <input type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($user_address['city'] ?? ''); ?>" required>
        <input type="text" name="state" placeholder="State" value="<?php echo htmlspecialchars($user_address['state'] ?? ''); ?>" required>
        <input type="text" name="postcode" placeholder="Postcode" value="<?php echo htmlspecialchars($user_address['postcode'] ?? ''); ?>" required>
        <input type="text" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($user_address['phone_number'] ?? ''); ?>" required>

        
		
		 <!-- Submit Billing Form -->
        <button type="submit" name="submit_billing" class="checkout-button">Update Billing Details</button>
</form>
 <form class="billing-form" method="POST" action="payment_method.php">
        <!-- Cart Summary -->
        <aside class="cart-summary">
            <h2 style="  font-family: 'Sorts Mill Goudy', serif;  ">Cart</h2>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                    <div>
                        <p><?php echo htmlspecialchars($item['product_name'] . ' in ' . $item['shade_name']); ?></p>
                        <p>MYR<?php echo number_format($item['item_total'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                <p><strong>Subtotal:</strong> MYR<?php echo number_format($total_price, 2); ?></p>
                <p><strong>Shipping:</strong> MYR10.00</p>
                <p><strong>Total:</strong> MYR<?php echo number_format($total_price + 10, 2); ?></p>
            </div>


            <!-- Submit Button -->
            <button type="submit" class="checkout-button">Place Order</button>
        </aside>
   </form>
</main>

 <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   
  <script src="js/script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap-4.4.1.js"></script>
</body>
</html>
