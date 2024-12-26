<?php 
session_start();
include('mysqli.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: LoginPage.php");
    exit(); 
}

$user_id = $_SESSION['user_id']; 
$cart_items = [];
$total_price = 0;

// Handle "Add to Cart" logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));
    $shade_id = isset($_POST['shade_id']) ? intval($_POST['shade_id']) : null;
    error_log("Adding to cart: User ID=$user_id, Product ID=$product_id, Shade ID=$shade_id, Quantity=$quantity");

    // Check if the item already exists in the cart
    $stmt = $conn->prepare("SELECT cart_id FROM cart_items WHERE user_id = ? AND product_id = ? AND shade_id = ?");
    if (!$stmt) {
        error_log("Failed to prepare SELECT statement: " . $conn->error);
        die("An error occurred. Please try again later.");
    }
    $stmt->bind_param("iii", $user_id, $product_id, $shade_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If item exists, update the quantity
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ?");
        if (!$stmt) {
            error_log("Failed to prepare UPDATE statement: " . $conn->error);
            die("An error occurred. Please try again later.");
        }
        $stmt->bind_param("ii", $quantity, $row['cart_id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, shade_id, quantity) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Failed to prepare INSERT statement: " . $conn->error);
            die("An error occurred. Please try again later.");
        }
        $stmt->bind_param("iiii", $user_id, $product_id, $shade_id, $quantity);
    }

    if (!$stmt->execute()) {
        error_log("Failed to execute statement: " . $stmt->error);
        die("Failed to update cart. Please try again.");
    }
    $stmt->close();

}
// Fetch Cart Items from Database
$stmt = $conn->prepare("
    SELECT 
        ci.cart_id, ci.quantity, p.product_id, p.product_name, p.product_price, 
        ps.shade_id, ps.shade_name, pi.image_url
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.product_id
    LEFT JOIN product_shades ps ON ci.shade_id = ps.shade_id
    LEFT JOIN product_images pi ON ps.shade_id = pi.shade_id AND pi.image_order = 1
    WHERE ci.user_id = ?
");
if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the cart items were fetched and calculate the total price
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $item_total = $row['product_price'] * $row['quantity'];
        $total_price += $item_total;

        // Add item to the cart array
        $cart_items[] = [
            'cart_id' => $row['cart_id'],
            'product_id' => $row['product_id'] ?? null,
            'product_name' => $row['product_name'] ?? 'Unknown Product',
            'shade_id' => $row['shade_id'] ?? null,
            'shade_name' => $row['shade_name'] ?? 'No Shade',
            'quantity' => $row['quantity'],
            'product_price' => $row['product_price'],
            'image_url' => $row['image_url'] ?? 'default-image.jpg',
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
    <title >Your Cart</title>
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
    <link href="ShoppingCart.css" rel="stylesheet" type="text/css">
	<style>
	/* Cart Layout */
.cart-wrapper {
    display: flex;
    justify-content: space-between;
    gap: 20px; 
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.cart-container {
    flex: 3; 
    border: 1px solid #ddd;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
}

.cart-summary {
    flex: 3; 
    border: 1px solid #ddd;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    text-align: center;
	
}

/* Cart Header */
.cart-header {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background-color: #f7f7f7;
    border-bottom: 2px solid #ccc;
}

.cart-header span {
    flex: 1;
    font-weight: bold;
    color: #333;
}

/* Cart Items */
.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    gap: 20px; 
}

/* Product Details */
.product-details {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 2;
}

.product-image {
    width: 80px;
    height: auto;
    border-radius: 5px;
    object-fit: cover;
}

.product-info {
    display: flex;
    flex-direction: column;
}

h2 {
    font-size: 18px;
    margin: 5px 0;
    color: #333;
}

p {
    margin: 5px 0;
    color: #555;
}

.price {
    color: #b12704;
    font-weight: bold;
}

.original-price {
    text-decoration: line-through;
    color: #777;
    margin-left: 10px;
}

/* Quantity Controls */
.quantity-wrapper {
    display: flex;
    align-items: center;
    gap: 5px; /* Space between elements */
    border: 1px solid #ccc;
    border-radius: 5px;
    width: fit-content;
    padding: 3px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    font-size: 18px;
    text-align: center;
    border: none;
    background-color: #f7f7f7;
    color: #333;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 3px;
    transition: background-color 0.2s;
}

.quantity-btn:hover {
    background-color: #e0e0e0;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: none;
    font-size: 16px;
    color: #333;
    -moz-appearance: textfield; /* Remove up/down arrows in Firefox */
}

/* Remove up/down arrows in Chrome */
.quantity-input::-webkit-inner-spin-button, 
.quantity-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input:focus {
    outline: none;
}

/* Total Price */
.item-total {
    flex: 1;
	color: #A55548;
    text-align: center; /* Align price in the center of its column */
    font-weight: bold;
}

.remove-btn {
    padding: 5px 10px;
    font-size: 14px;
    border: 1px solid #f00;
    color: #f00;
    background: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    background-color: #f00;
    color: white;
}
.cart-summary {
    padding: 20px;
    background-color: #f9f9f9;
		justify-content: center;

    border: 1px solid #ddd;
    border-radius: 5px;
    width: 300px; 
    margin-top: 20px;
}

.cart-summary h2 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.cart-summary .total-amount {
    font-size: 20px;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 15px;
}

.cart-summary p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.cart-summary .checkout, .buttonCart {
    background-color: #64341c;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    width: 100%; 
    transition: background-color 0.3s ease;
}

.cart-summary .checkout:hover {
    background-color:#643c1f;
}


/* Responsive Design */
@media screen and (max-width: 768px) {
    .cart-wrapper {
        flex-direction: column;
    }

    .cart-items {
        width: 100%;
    }

    .cart-summary {
        width: 100%;
        margin-top: 20px;
    }

    .cart-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .cart-item img {
        width: 60px;
    }

    .quantity {
        margin-top: 10px;
    }

    .total-price {
        text-align: left;
        margin-top: 10px;
    }

    .remove {
        align-self: flex-end;
        margin-top: 10px;
    }
}

 .nav1 a {
            color: black;
            text-decoration: none; 
        }

        .nav1 a:hover {
            color: darkgray; 
        }
	</style>
</head>
<body>
  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   
<br>
     <div class="container3">
        <nav class="nav1">
            <a href="home.php">Home</a> / <a href="ShoppingCart.php">Cart</a>
        </nav>
<h1 class="title" style="font-family: 'Sorts Mill Goudy', serif;  color: #A55548;  font-size: 36px; margin-top: 20px; margin-bottom: 20px;">Shopping Cart</h1>

       <div class="cart">
    <div class="cart-items">
     

<div class="cart-container">
    <!-- Update Cart Form -->
<div class="cart-container"> 
    <!-- Update Cart Form -->
    <form method="POST" action="update_cart.php" id="updateCartForm">
        <input type="hidden" name="action" value="update_all">
        <div class="cart-items">
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $index => $item): ?>
                    <div class="cart-item">
                        <!-- Product Image -->
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                             class="product-image">

                        <!-- Item Details -->
                        <div class="item-details">
                            <h2><?php echo htmlspecialchars($item['product_name'] . ' in ' . $item['shade_name']); ?></h2>
                            <p class="price">RM<?php echo number_format($item['product_price'], 2); ?></p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="quantity-wrapper">
                            <button type="button" class="quantity-btn minus">−</button>
                            <input type="number" name="cart[<?php echo $index; ?>][quantity]" 
                                   value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                   min="1" max="10" class="quantity-input" 
                                   data-product-id="<?php echo $item['product_id']; ?>" 
                                   data-shade-id="<?php echo $item['shade_id']; ?>">
                            <button type="button" class="quantity-btn plus">+</button>
                        </div>

                        <!-- Total Price -->
                        <p class="total-price">RM<?php echo number_format($item['item_total'], 2); ?></p>

                        
                        <!-- Remove Button Inside the Same Form -->
                    <button type="button" class="remove-btn" data-product-id="<?php echo $item['product_id']; ?>" 
                            data-shade-id="<?php echo $item['shade_id']; ?>">Remove</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
<br>
        <!-- Update Cart Button -->
       <button type="button" id="updateCartBtn" class="buttonCart">Update Cart</button>
    </form>

    <div class="cart-summary">
        <h2>Total</h2>
        <p class="total-amount">RM<?php echo number_format($total_price, 2); ?></p>
        <p>Shipping calculated at checkout</p>
        <button class="checkout" onclick="location.href='checkout.php'">Check Out</button>
    </div>
</div>
</div>
    </div>
</div>
</div>
<br> <br>
  <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Find all "Remove" buttons
    document.querySelectorAll('.remove-btn').forEach(function (button) {
        button.addEventListener('click', function (event) {
            // Prevent default button behavior
            event.preventDefault();

            // Get the product_id and shade_id from data attributes
            const productId = this.getAttribute('data-product-id');
            const shadeId = this.getAttribute('data-shade-id');

            // Get the form element
            const form = document.getElementById('updateCartForm');

            // Set the action for the form to "remove"
            form.querySelector('input[name="action"]').value = 'remove';

            // Remove any previously set hidden inputs for product_id and shade_id
            let existingProductIdInput = form.querySelector('input[name="product_id"]');
            let existingShadeIdInput = form.querySelector('input[name="shade_id"]');
            if (existingProductIdInput) existingProductIdInput.remove();
            if (existingShadeIdInput) existingShadeIdInput.remove();

            // Create new hidden inputs for product_id and shade_id
            let productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = productId;
            form.appendChild(productIdInput);

            let shadeIdInput = document.createElement('input');
            shadeIdInput.type = 'hidden';
            shadeIdInput.name = 'shade_id';
            shadeIdInput.value = shadeId;
            form.appendChild(shadeIdInput);

            // Submit the form to the server to remove the item
            form.submit();
        });
    });
});

</script>

<script>
document.addEventListener("DOMContentLoaded", () => { 
    // Function to update the total price dynamically
    const updateTotalPrice = () => {
        let total = 0;
        document.querySelectorAll(".cart-item").forEach((item) => {
            const priceElement = item.querySelector(".price");
            const quantityInput = item.querySelector(".quantity-input");
            const itemTotalElement = item.querySelector(".total-price");

            // Parse price and quantity
            const price = parseFloat(priceElement.textContent.replace("RM", "").trim()); // Parsing price
            const quantity = parseInt(quantityInput.value, 10) || 0; // Get quantity

            // Update item's total price
            const itemTotal = price * quantity;
            itemTotalElement.textContent = `RM${itemTotal.toFixed(2)}`;

            // Add item's total to the overall total
            total += itemTotal;
        });

        // Update the total price on the page
        document.querySelector(".total-amount").textContent = `RM${total.toFixed(2)}`;
    };

    // Add event listeners for quantity buttons (minus, plus)
    document.querySelectorAll(".quantity-wrapper").forEach((wrapper) => {
        const minusBtn = wrapper.querySelector(".minus");
        const plusBtn = wrapper.querySelector(".plus");
        const input = wrapper.querySelector(".quantity-input");

        // Decrease quantity (minus button)
        minusBtn.addEventListener("click", () => {
            let currentValue = parseInt(input.value, 10) || 1;
            if (currentValue > parseInt(input.min, 10)) {
                input.value = currentValue - 1; // Decrease the value
                updateTotalPrice(); // Update total dynamically
            }
        });

        // Increase quantity (plus button)
        plusBtn.addEventListener("click", () => {
            let currentValue = parseInt(input.value, 10) || 1;
            if (currentValue < parseInt(input.max, 10)) {
                input.value = currentValue + 1; // Increase the value
                updateTotalPrice(); // Update total dynamically
            }
        });

        // Update total price when quantity is manually changed in the input box
        input.addEventListener("change", () => {
            let value = parseInt(input.value, 10) || 1;
            const min = parseInt(input.min, 10);
            const max = parseInt(input.max, 10);

            // Clamp value between min and max
            if (value < min) {
                input.value = min;
            } else if (value > max) {
                input.value = max;
            }

            updateTotalPrice(); // Update total dynamically
        });
    });

    // Initial total price calculation
    updateTotalPrice();
});

</script>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const updateCartBtn = document.getElementById('updateCartBtn'); // Get the update button

    updateCartBtn.addEventListener('click', (event) => {
        const updatedCartData = {};

        document.querySelectorAll('.quantity-input').forEach((input, index) => {
            const productId = input.getAttribute('data-product-id');
            const shadeId = input.getAttribute('data-shade-id');
            const quantity = parseInt(input.value, 10);

            if (productId && shadeId && quantity >= 1) {
                updatedCartData[index] = {
                    product_id: productId,
                    shade_id: shadeId,
                    quantity: quantity
                };
            }
        });

        if (Object.keys(updatedCartData).length === 0) {
            event.preventDefault(); // Prevent form submission
            alert('Please update quantities before submitting.');
            return;
        }

        const form = document.getElementById('updateCartForm');
        form.querySelector('input[name="action"]').value = 'update_all'; // Set the action for update

        Object.keys(updatedCartData).forEach((index) => {
            const cartItem = updatedCartData[index];

            let productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `cart[${index}][product_id]`;
            productIdInput.value = cartItem.product_id;
            form.appendChild(productIdInput);

            let shadeIdInput = document.createElement('input');
            shadeIdInput.type = 'hidden';
            shadeIdInput.name = `cart[${index}][shade_id]`;
            shadeIdInput.value = cartItem.shade_id;
            form.appendChild(shadeIdInput);

            let quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `cart[${index}][quantity]`;
            quantityInput.value = cartItem.quantity;
            form.appendChild(quantityInput);
        });

        form.submit(); // Submit the form
    });
});

</script>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>