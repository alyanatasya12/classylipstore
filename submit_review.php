<?php 
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'mysqli.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $reviewer_name = isset($_POST['reviewer_name']) ? trim(htmlspecialchars($_POST['reviewer_name'])) : '';
    $review_text = isset($_POST['review_text']) ? trim(htmlspecialchars($_POST['review_text'])) : '';
    $star_rating = isset($_POST['star_rating']) ? (int) $_POST['star_rating'] : 0; // Rating value
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0; // Product ID

    // Validate inputs
    if (empty($reviewer_name) || empty($review_text) || $star_rating < 1 || $star_rating > 5 || $product_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data. Please check all fields.']);
        exit;
    }

    // Check if the product exists in the database
    $stmt = $conn->prepare('SELECT COUNT(*) FROM products WHERE product_id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    // Insert the review into the database
    try {
        $stmt = $conn->prepare('INSERT INTO product_reviews (reviewer_name, review_text, star_rating, review_date, product_id) 
                               VALUES (?, ?, ?, NOW(), ?)');
        $stmt->bind_param('ssii', $reviewer_name, $review_text, $star_rating, $product_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error inserting review: ' . $e->getMessage()]);
    }
}
?>


