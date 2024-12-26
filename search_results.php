<?php
include('mysqli.php'); 

$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($searchQuery)) {
    $query = "%" . mysqli_real_escape_string($conn, $searchQuery) . "%"; 

    $sql = "SELECT 
                p.product_id, 
                p.product_name, 
                ps.shade_name, 
                pi.image_url AS image, 
                pi.image_order, 
                p.product_price,
                ps.shade_id
            FROM products p
            LEFT JOIN product_shades ps ON p.product_id = ps.product_id
            LEFT JOIN product_images pi ON ps.shade_id = pi.shade_id
            WHERE p.product_name LIKE ? OR ps.shade_name LIKE ?
            ORDER BY p.product_name, ps.shade_name, pi.image_order";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch all shades
        $shades = [];
        while ($row = $result->fetch_assoc()) {
            // Group by product_id and shade_id
            $key = $row['product_id'] . '-' . $row['shade_id'];
            if (!isset($shades[$key])) {
                // Only store the first image per shade for the same product
                $shades[$key] = $row;
            }
        }
    } else {
        echo "No products found.";
    }

    $stmt->close();
}
?>


		
		

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>search</title>

    <!-- Linking external fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700"> 
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap-4.4.1.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link href="lipscategory1.css" rel="stylesheet" type="text/css"> <!-- Custom CSS -->


<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>  

 <br><br>
<div class="container mt-3">
<div class="text-center">	  
            <h2 style="    color: #A55548;  
font-family: 'Sorts Mill Goudy', serif;
 ">Search Results for: <?= htmlspecialchars($searchQuery); ?></h2><br></div></div>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Search Results</title>

            <link href="search_results.css" rel="stylesheet" type="text/css"> 
        </head>

<div id="new-arrivals" class="container">
    <?php if (!empty($shades)): ?>
        <?php foreach (array_chunk($shades, 4) as $row): ?>
            <div class="row justify-content-center text-center">
                <?php foreach ($row as $shade): ?>
                    <!-- Product Card -->
                    <div class="col-6 col-md-3 pb-3">
                        <div class="card">
                            <a href="product-details.php?product_id=<?= htmlspecialchars($shade['product_id']); ?>&shade_id=<?= htmlspecialchars($shade['shade_id']); ?>">
                                <img 
                                    class="card-img-top" 
                                    src="<?= htmlspecialchars($shade['image']); ?>" 
                                    alt="<?= htmlspecialchars($shade['shade_name']); ?>" 
                                    loading="lazy" 
                                    onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title" style="color: black;">
                                    <?= htmlspecialchars($shade['product_name']); ?> <br> in <?= htmlspecialchars($shade['shade_name']); ?>
                                </h5>
                                <p class="card-text" style="color: black;">
                                    RM<?= number_format($shade['product_price'], 2); ?>
                                </p>
                                <form method="POST" action="ShoppingCart.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($shade['product_id']); ?>">
                                    <input type="hidden" name="shade_id" value="<?= htmlspecialchars($shade['shade_id']); ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="buttonCart">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No results found for '<strong><?= htmlspecialchars($searchQuery); ?></strong>'.</p>
    <?php endif; ?>
</div>


<?php
	include('includes/footer.php');
?>  
