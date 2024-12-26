<?php
// Include your database connection
include('mysqli.php');

// Check if the search query is sent via GET (for form submission or AJAX request)
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['query']); // Sanitize input

    // Query to search for products in the database
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$searchQuery%' OR product_description LIKE '%$searchQuery%'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // If AJAX request, return the results as HTML
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li><a href='product-details.php?product_id=" . $row['product_id'] . "'>" . $row['product_name'] . "</a></li>";
            }
        } else {
            // If form submission, display results on the page
            echo "<h2>Search Results for: " . htmlspecialchars($searchQuery) . "</h2>";
            echo "<ul>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li><a href='product-details.php?product_id=" . $row['product_id'] . "'>" . $row['product_name'] . "</a></li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p>No results found for '" . htmlspecialchars($searchQuery) . "'.</p>";
    }
} else {
    echo "<p>Please enter a search term.</p>";
}

// Close the database connection
mysqli_close($conn);
?>
