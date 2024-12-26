<?php
session_start();

include('mysqli.php');

// Fetch 3 products from subcategory_id = 11
$shopMoreQuery = "
    SELECT 
        ps.shade_id,
        ps.shade_name,
        ps.shade_color_code,
        ps.product_id,
        p.product_name,
        p.product_price,
        (
            SELECT pi.image_url 
            FROM product_images pi 
            WHERE pi.shade_id = ps.shade_id 
            ORDER BY pi.image_order ASC
            LIMIT 1
        ) AS shade_image
    FROM product_shades ps
    INNER JOIN products p ON ps.product_id = p.product_id
    WHERE p.subcategory_id = 6
    ORDER BY p.created_at DESC
    LIMIT 3
";
$shopMoreResult = $conn->query($shopMoreQuery);

if (!$shopMoreResult) {
    die("Query error: " . $conn->error);
}

$shopMore = [];

// Fetch data for Shop More
while ($row = $shopMoreResult->fetch_assoc()) {
    $shopMore[] = [
        'shade_id' => $row['shade_id'],
        'shade_name' => $row['shade_name'],
        'shade_color_code' => $row['shade_color_code'],
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'image' => $row['shade_image'] ?? '/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg',
    ];
}

// Ensure all quiz answers are available in the session
if (!isset($_SESSION['question1'], $_SESSION['question2'], $_SESSION['question3'])) {
    echo "<p>Please complete the quiz before viewing your results.</p>";
    exit();
}

// Fetch the shade IDs from session for each question
$blush_shade_id = $_SESSION['question1']['shade_id'] ?? null;
$lip_shade_id = $_SESSION['question2']['shade_id'] ?? null;
$eye_shade_id = $_SESSION['question3']['shade_id'] ?? null;


if (!$blush_shade_id || !$lip_shade_id || !$eye_shade_id) {
    echo "<p>Invalid shade selection. Please retake the quiz.</p>";
    exit();
}

// fetch product details based on shade_id
function getShadeDetails($shade_id, $conn) {
    $query = "
        SELECT 
            ps.shade_id, 
            ps.shade_name, 
            ps.shade_color_code, 
            ps.product_id, 
            p.product_name, 
            p.product_price,
            (SELECT pi.image_url 
             FROM product_images pi 
             WHERE pi.product_id = ps.product_id 
             ORDER BY pi.image_order ASC 
             LIMIT 1) AS shade_image
        FROM 
            product_shades ps
        INNER JOIN 
            products p 
            ON ps.product_id = p.product_id
        WHERE 
            ps.shade_id = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p>Database error: Unable to prepare query.</p>";
        return null;
    }

    $stmt->bind_param('i', $shade_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>No product found for shade_id: $shade_id</p>";
        $stmt->close();
        return null;
    }

    $shade_details = $result->fetch_assoc();
    $stmt->close();

    return $shade_details;
}

// Fetch details for blush, lip, and eye shades
$blush_shade = getShadeDetails($blush_shade_id, $conn);
$lip_shade = getShadeDetails($lip_shade_id, $conn);
$eye_shade = getShadeDetails($eye_shade_id, $conn);

// Check if all shades were fetched successfully
if (!$blush_shade || !$lip_shade || !$eye_shade) {
    echo "<p>Some shade details could not be retrieved. Please try again later.</p>";
    exit();
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find My Shade - Results</title>
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
    <link href="FindMyShades1.css" rel="stylesheet" type="text/css">
</head>
  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   
<br><br>
<body>
<div class="quiztext">
    <h2 class="QuizHeader">Your Personalized Shade Results</h2>
    <div class="text-center d-flex justify-content-center">
    </div>
</div>


<div class="row justify-content-center">
    <!-- Lips Suggestion -->
    <div class="col-md-4 text-center"  style="padding: 0 10px;">
<div class="title" style="text-align: center; margin: 20px 0;">
    <h3 style="      	font-style: italic; font-family: 'Sorts Mill Goudy', serif;  font-size: 30px; font-weight: bold; color: #A55548; ">
        Lips </h3>
</div>
        <?php if ($lip_shade): ?>
            <p>We recommend the <b><?= htmlspecialchars($lip_shade['product_name']); ?> in <?= htmlspecialchars($lip_shade['shade_name']); ?></b><br> for long-lasting, glossy lips.</p>
            <div class="d-flex justify-content-center">
                <div class="col-6 pb-4">
                    <div class="card">
                        <a href="product-details.php?product_id=<?= htmlspecialchars($lip_shade['product_id']); ?>&shade_id=<?= htmlspecialchars($lip_shade['shade_id']); ?>">
                            <img class="card-img-top" 
                                 src="<?= htmlspecialchars($lip_shade['shade_image']); ?>" 
                                 alt="<?= htmlspecialchars($lip_shade['shade_name']); ?>"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($lip_shade['product_name']); ?> in <?= htmlspecialchars($lip_shade['shade_name']); ?></h5>
                            <p class="card-text">RM<?= number_format($lip_shade['product_price'], 2); ?></p>
                            <form method="POST" action="ShoppingCart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($lip_shade['product_id']); ?>">
                                <input type="hidden" name="shade_id" value="<?= htmlspecialchars($lip_shade['shade_id']); ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buttonCart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Sorry, no suggestions for lips at the moment.</p>
        <?php endif; ?>
    </div>
	

    <!-- Blush Suggestion -->
    <div class="col-md-4 text-center"  style="padding: 0 10px;">
 <div class="title" style="text-align: center; margin: 20px 0;">
    <h3 style="      	font-style: italic;
 font-family: 'Sorts Mill Goudy', serif;  
font-size: 30px; font-weight: bold; color: #A55548; ">
        Cheeks
    </h3>
</div>
        <?php if ($blush_shade): ?>
            <p>We recommend the <b><?= htmlspecialchars($blush_shade['product_name']); ?> in <?= htmlspecialchars($blush_shade['shade_name']); ?></b><br> for a natural and dewy glow.</p>
            <div class="d-flex justify-content-center">
                <div class="col-6 pb-3">
                    <div class="card">
                        <a href="product-details.php?product_id=<?= htmlspecialchars($blush_shade['product_id']); ?>&shade_id=<?= htmlspecialchars($blush_shade['shade_id']); ?>">
                            <img class="card-img-top" 
                                 src="<?= htmlspecialchars($blush_shade['shade_image']); ?>" 
                                 alt="<?= htmlspecialchars($blush_shade['shade_name']); ?>"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($blush_shade['product_name']); ?> in <?= htmlspecialchars($blush_shade['shade_name']); ?></h5>
                            <p class="card-text">RM<?= number_format($blush_shade['product_price'], 2); ?></p>
                            <form method="POST" action="ShoppingCart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($blush_shade['product_id']); ?>">
                                <input type="hidden" name="shade_id" value="<?= htmlspecialchars($blush_shade['shade_id']); ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buttonCart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Sorry, no suggestions for cheeks at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Eyeshadow Suggestion -->
    <div class="col-md-4 text-center"  style="padding: 0 10px;">
<div class="title" style="text-align: center; margin: 20px 0;">
    <h3 style="      	font-style: italic;
 font-family: 'Sorts Mill Goudy', serif;  
font-size: 30px; font-weight: bold; color: #A55548; ">
        Eyeshadow
    </h3>
</div>        <?php if ($eye_shade): ?>
            <p>We recommend the <b><?= htmlspecialchars($eye_shade['product_name']); ?> in <?= htmlspecialchars($eye_shade['shade_name']); ?></b> <br>for a dramatic, smoky eye.</p>
            <div class="d-flex justify-content-center">
                <div class="col-6 pb-3">
                    <div class="card">
                        <a href="product-details.php?product_id=<?= htmlspecialchars($eye_shade['product_id']); ?>&shade_id=<?= htmlspecialchars($eye_shade['shade_id']); ?>">
                            <img class="card-img-top" 
                                 src="<?= htmlspecialchars($eye_shade['shade_image']); ?>" 
                                 alt="<?= htmlspecialchars($eye_shade['shade_name']); ?>"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($eye_shade['product_name']); ?> in <?= htmlspecialchars($eye_shade['shade_name']); ?></h5>
                            <p class="card-text">RM<?= number_format($eye_shade['product_price'], 2); ?></p>
                            <form method="POST" action="ShoppingCart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($eye_shade['product_id']); ?>">
                                <input type="hidden" name="shade_id" value="<?= htmlspecialchars($eye_shade['shade_id']); ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buttonCart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Sorry, no suggestions for eyes at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-3">
 <div class="row"></div>
<hr>
<h1 class="Product">Shop More</h1>
<hr>
    <div id="new-arrivals" class="row text-center">
      
        <?php foreach ($shopMore as $product): ?>
           <div class="col-6 col-md-4 pb-4">
    <div class="card">
        <a href="product-details.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>">
            <img 
                class="card-img-top primary-image" 
                src="<?= htmlspecialchars($product['image']); ?>" 
                alt="<?= htmlspecialchars($product['shade_name']); ?>" 
                loading="lazy" 
                onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
            <img 
                class="card-img-top secondary-image" 
                src="path-to-second-image.jpg" 
                alt="Secondary Image" 
                loading="lazy" 
                style="display: none;">
        </a>
        <div class="card-body">
            <h5 class="card-title" style="color: black; font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">
                <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
            </h5>
            <p class="card-text" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;    font-size: 12px; 
">
                RM<?= number_format($product['product_price'], 2); ?>
            </p>
            <form method="POST" action="ShoppingCart.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                <input type="hidden" name="shade_id" value="<?= htmlspecialchars($product['shade_id']); ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="buttonCart">Add to Cart</button>
            </form>
        </div>
    </div>
</div>
        <?php endforeach; ?>
    </div>
</div>

	<br>	<br>
    <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   

</body>
</html>



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>
