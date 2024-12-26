<?php 
session_start();
include('mysqli.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php");
    exit();
}

// Get the payment method 
if (!isset($_POST['payment-method']) || empty($_POST['payment-method'])) {
    die("Payment method not provided.");
}

$payment_method = $_POST['payment-method'];

// bank_logo
$bank_logos = [
    "CIMB Bank" => "bank_logo/cimbbank.png",
    "Maybank" => "bank_logo/mbb.png",
    "Public Bank" => "bank_logo/publicbanklogo.png",
    "Bank Islam" => "bank_logo/bankislam.png",
	"GrabPay" => "bank_logo/grabpay.png",
	"Apple Pay" => "bank_logo/applepay.png",
];

$bank_logo = $bank_logos[$payment_method] ?? 'bank_logo/default-bank-logo.png'; 

// Fetch user's cart items and calculate the total price
$user_id = $_SESSION['user_id'];

// Fetch the user's cart items to calculate the total price
$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;

// Get cart items for the user
$stmt = $conn->prepare("SELECT p.product_price, ci.quantity FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $total_price += $row['product_price'] * $row['quantity'];
    $cart_items[] = $row; 
}

// Handle form submission
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['bank_username'], $_POST['password'])) {
        $bank_username = $_POST['bank_username'];
        $bank_password = $_POST['password'];

    
        if (!empty($bank_username) && !empty($bank_password)) {
     
            $password_hash = password_hash($bank_password, PASSWORD_DEFAULT);

            // Insert bank details along with the payment method and total price
            $stmt = $conn->prepare("INSERT INTO bank_info (user_id, payment_method, bank_username, bank_password_hash, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssd", $user_id, $payment_method, $bank_username, $password_hash, $total_price);

            if ($stmt->execute()) {
                // Redirect to the process checkout page
                header("Location: process_checkout.php");
                exit();
            } else {
                $error = "Error saving bank information.";
            }
        } else {
            $error = "Please fill in both fields.";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Payment Approval</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
  font-family:Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"	;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container4 {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .bank-logo {
            width: 150px;
            display: block;
            margin: 0 auto 20px;
        }
        .payment-message {
            text-align: center;
            margin-bottom: 30px;
        }
        .payment-message h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        .payment-message p {
            font-size: 14px;
            color: #666;
        }
        .payment-details p {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .payment-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            font-size: 16px;
            color: #555;
        }
        .form-control {
            border: 2px solid #ddd;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #5cb85c;
            box-shadow: 0 0 5px rgba(0, 128, 0, 0.2);
        }
        .btn-approve {
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
        .btn-approve:hover {
            background-color: #64341c;
        }
        .alert {
            background-color: #e3b49c;
            color: #64341c;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
<form action="process_checkout.php" method="POST">
    <div class="container4"><br><br>
        <img src="<?php echo htmlspecialchars($bank_logo); ?>" alt="Bank Logo" class="bank-logo">


        <div class="payment-message">
            <h2>Complete Your Payment</h2>
            <p>Please enter your bank details below to approve your payment.</p>
        </div>

        <?php if ($total_price): ?>
            <div class="payment-details">
                <p><strong>Pay to:</strong> ClassyLip</p>
                <p><strong>Total Amount:</strong> RM<?= number_format($total_price + 10, 2) ?></p>
         
                <p><strong>Payment Method: </strong> <?= htmlspecialchars($payment_method) ?></p>
            </div>
        <?php else: ?>
            <div class="payment-details">
                <p><strong>Error:</strong> No valid order found.</p>
            </div>
        <?php endif; ?>

  
        <input type="hidden" name="payment-method" value="<?= htmlspecialchars($payment_method) ?>">

      
         <div class="alert">
            <strong>Note:</strong> Please ensure your details are correct before submitting.
        </div>

        <div class="form-group">
            <label for="bank_username">Username</label>
            <input type="text" id="bank_username" name="bank_username" class="form-control" placeholder="Enter your username" required>
        </div>
<div class="form-group">
  <label for="password">
    Password
    <span class="privacy-icon" aria-hidden="true">***</span>
  </label>
  <input 
    type="password" 
    id="password" 
    name="password" 
    class="form-control" 
    placeholder="Enter your password" 
    required
  >
  <small class="form-text text-muted">
    Your information is encrypted and secure.
  </small>
</div>

        <button type="submit" class="btn-approve">Submit Bank Info</button>
    </div>
</form>



    <!-- jQuery and Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
