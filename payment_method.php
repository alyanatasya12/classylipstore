<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php?redirect=checkout.php");
    exit();
}

// Fetch cart details and total price from the database
include('mysqli.php');
$user_id = $_SESSION['user_id'];
$total_price = 0;

// Fetch cart items, shade_name, and image_url
$stmt = $conn->prepare("
    SELECT 
        ci.product_id, 
        ci.shade_id, 
        ci.quantity, 
        p.product_name, 
        p.product_price, 
        ps.shade_name, 
        pi.image_url
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.product_id
    INNER JOIN product_shades ps ON ci.shade_id = ps.shade_id
    LEFT JOIN product_images pi ON ci.product_id = pi.product_id AND pi.shade_id = ci.shade_id
    WHERE ci.user_id = ?
    GROUP BY ci.product_id, ci.shade_id
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $total_price += $row['product_price'] * $row['quantity'];
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    die("Your cart is empty. Please add items to your cart before proceeding to checkout.");
}
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700">
    <link href="paymentmethod.css" rel="stylesheet" type="text/css">
</head>
<style>
.payment-option {
  display: flex;
  align-items: center;
  gap: 12px; /* Spacing between radio button and label */
  margin-bottom: 12px; /* Spacing between each payment option */
}

.payment-option input[type="radio"] {
  margin: 0;
}

.inline-label {
  display: grid;
  grid-template-columns: 50px auto; 
  align-items: center;
  gap: 12px;
  cursor: pointer;
}

.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 50px; /* Ensures a consistent height for the logo container */
}

.bank-logo {
  width: 40px;
  height: auto;
}

.text-container {
  display: flex;
  align-items: center;
}

.accordion {
  display: flex;
  align-items: center;
  gap: 12px; /* Space between the logo and the text */
  padding: 10px 20px;
  background-color: #f1f1f1;
  border: none;
  cursor: pointer;
  font-size: 16px;
}


</style>

<body>
    <div class="container">
        <!-- Header -->
       <div class="header" >
<h1 style="font-family: Sorts Mill Goudy, serif; color: #A55548;">Checkout</h1>
        <button id="toggle-summary">Hide Summary</button>
      </div>
      
     

        <!-- Order Summary -->
        <div class="summary" id="summary">
   <?php foreach ($cart_items as $item): ?>
    <div class="summary-item" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center;">
            <!-- Product Image (only one image per product-shade combination) -->
            <img src="<?= htmlspecialchars($item['image_url'] ?: 'images/placeholder.jpg'); ?>" 
                 alt="<?= htmlspecialchars($item['product_name']); ?>" 
                 style="width: 80px; height: auto; margin-right: 15px;">
            <div class="summary-item-details">
                <p><strong><?= htmlspecialchars($item['product_name'] . ' in ' . $item['shade_name']); ?></strong></p>
                <p><strong>Qty:</strong> <?= $item['quantity']; ?></p>
                <p><strong>Unit Price:</strong> RM<?= number_format($item['product_price'], 2); ?></p>
            </div>
        </div>
        <div style="font-size: 16px; font-weight: bold; color: #333; text-align: right;">
            <p>RM<?= number_format($item['product_price'] * $item['quantity'], 2); ?></p>
        </div>
    </div>
<?php endforeach; ?>
    <div class="order-totals">
        <p><strong>Subtotal:</strong> RM<?= number_format($total_price, 2); ?></p>
        <p><strong>Shipping:</strong> RM10.00</p>
        <p><strong>Total:</strong> RM<?= number_format($total_price + 10, 2); ?></p>
    </div>
</div>

        <!-- Payment Methods -->
        <form action="bank.php" method="POST">
    <div class="payment-method">
        <h2 style="font-family: 'Sorts Mill Goudy', serif;     color: #A55548;  ">Select Payment Method</h2>

        <!-- Online Banking -->
    <button type="button" class="accordion" style="font-family: 'Sorts Mill Goudy', serif;">
  <img src="images/bank_logo/fpx.png" alt="Online Banking Icon width="25" height="25" class="mx-1" class="button-icon">
  Online Banking
</button>

		<div class="panel" class="accordion" style= "  font-family: 'Sorts Mill Goudy', serif; font-size: 14px; color: #333"></label>
		
		<div class="payment-option">
            <input type="radio" id="CIMB Bank" name="payment-method" value="CIMB Bank">
			<label for="CIMB_Bank" class="inline-label" style="  gap: 35px;">
            <img src="images/bank_logo/cimbbank.png" alt="CIMB Clicks width="25" height="25" class="mx-1">		  
          <label for="CIMB Bank">CIMB Bank</label></label><br></div>
		  
		<div class="payment-option">
		  <input type="radio" id="Maybank" name="payment-method" value="Maybank">
		  <label for="Maybank" class="inline-label">
			<img src="images/bank_logo/mbb.png" alt="Maybank" class="bank-logo" width="70" height="30">
			Maybank
		  </label>
		</div>

		<div class="payment-option">
		  <input type="radio" id="Public_Bank" name="payment-method" value="Public Bank">
		  <label for="Public_Bank" class="inline-label">
			<img src="images/bank_logo/publicbanklogo.png" alt="Public Bank" class="bank-logo" width="45" height="45">
			Public Bank
		  </label>
		</div>

		<div class="payment-option">
		  <input type="radio" id="bank-islam" name="payment-method" value="Bank Islam">
		  <label for="bank-islam" class="inline-label">
			<img src="images/bank_logo/bankislam.png" alt="Bank Islam" class="bank-logo" width="50" height="15">
			Bank Islam
		  </label>
		</div>
           
        </div>

        <!-- E-Wallet -->
		
			<button type="button" class="accordion" style="font-family: 'Sorts Mill Goudy', serif;">
  <img src="images/bank_logo/grabpay.png"  alt="grabpay" class="button-icon" width="40" height="10">
    <img src="images/bank_logo/applepay.png"  alt="applepay" class="button-icon" width="30" height="25">
  E-Wallet
</button>

			   
		<div class="panel" style="font-family: 'Sorts Mill Goudy', serif; font-size: 14px; color: #333">
		  
		  <div class="payment-option">
			<input type="radio" id="grabpay" name="payment-method" value="GrabPay">
			<label for="grabpay" class="inline-label">
			  <img src="images/bank_logo/grabpay.png" alt="GrabPay" width="30" height="30" class="bank-logo">
			  GrabPay
			</label>
		  </div>

		  <div class="payment-option">
			<input type="radio" id="applepay" name="payment-method" value="Apple Pay">
			<label for="applepay" class="inline-label">
			  <img src="images/bank_logo/applepay.png" alt="Apple Pay" width="25" height="25" class="bank-logo">
			  Apple Pay
			</label>
		  </div>

		</div>
		<br>

    <!-- Proceed Button -->
    <button type="submit" class="btn" style="font-family: Sorts Mill Goudy, serif;"> <b>Proceed to Payment
	</b></button>
</form>


   <script>
      const summary = document.getElementById('summary');
      const toggleBtn = document.getElementById('toggle-summary');

      toggleBtn.addEventListener('click', () => {
        if (summary.style.display === 'none') {
          summary.style.display = 'block';
          toggleBtn.textContent = 'Hide Summary';
        } else {
          summary.style.display = 'none';
          toggleBtn.textContent = 'Show Summary';
        }
      });

      // Accordion functionality - Close other panels when one is clicked
      const accordions = document.querySelectorAll('.accordion');
      accordions.forEach(accordion => {
        accordion.addEventListener('click', function() {
          // Close other panels
          const allPanels = document.querySelectorAll('.panel');
          allPanels.forEach(panel => {
            if (panel !== this.nextElementSibling) {
              panel.style.display = 'none';
            }
          });
          // Toggle the current panel
          const panel = this.nextElementSibling;
          if (panel.style.display === 'block') {
            panel.style.display = 'none';
          } else {
            panel.style.display = 'block';
          }
        });
      });

      function proceedToPayment() {
        const selectedMethod = document.querySelector('input[name="payment-method"]:checked');
        if (selectedMethod) {
          const method = selectedMethod.value;
          if (method === 'bank1') {
            window.location.href = 'online-banking-cimb.html';
          } else if (method === 'bank2') {
            window.location.href = 'online-banking-maybank.html';
          } else if (method === 'bank3') {
            window.location.href = 'online-banking-publicbank.html';
          } else if (method === 'bank4') {
            window.location.href = 'online-banking-rhb.html';
          } else if (method === 'grabpay') {
            window.location.href = 'grabpay.html';
          } else if (method === 'tng') {
            window.location.href = 'tng.html';
          } else if (method === 'applepay') {
            window.location.href = 'applepay.html';
          } else {
            alert('You selected: ' + method);
          }
        } else {
          alert('Please select a payment method.');
        }
      }
    </script>
  </body>
</html>