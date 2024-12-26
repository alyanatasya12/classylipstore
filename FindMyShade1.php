<?php 
session_start();
include('mysqli.php');

// Fetch products from subcategory_id = 11
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
    WHERE p.subcategory_id = 11
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['color'])) {
    $color = $_POST['color'];

    // shade_id  for blush
    switch ($color) {
        case 'pink':
            $shade_id = 212; // Blushing Bride
            break;
        case 'coral':
            $shade_id = 214; // Peaceful
            break;
        case 'red':
            $shade_id = 213; // Charisma
            break;
        default:
            $shade_id = null; 
    }

    
    $_SESSION['question1'] = [
        'product_id' => 188, // product ID
        'shade_id' => $shade_id,
        'color' => $color
    ];
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    // Redirect to the next question
    header("Location: FindMyShade2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >Find My Shade</title>
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

  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

	
	<br>
 <div class="container-fluid">
     
		<div class= "quiztext">
<h2 class="QuizHeader"  style="font-size: 36spx; 
">Find Your Best Blush Color</h2></div>
                    <div class="text-center d-flex justify-content-center">
<p class="Description" style="font-size: 16px; 
">Which blush shade are you looking for?? Select the best option so that we can provide you with the item that most accurately fits your needs. </p>
</div><hr>
    </div>
	
<form method="POST">
    <div class="container mt-3">
        <div class="row text-center justify-content-center">
            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="pink" class="borderless-button">
                    <div class="card">
                        <img src="images/bride blusher/bride1.jpg" alt="Fair Skin Tone" class="card-img-top zoom-effect blush-image" />
                        <div class="card-body">
                            <h5 class="card-title">Pink</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="212" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="coral" class="borderless-button">
                    <div class="card">
                        <img src="images/charisma blusher/charisma3.jpg" alt="Medium Skin Tone" class="card-img-top zoom-effect blush-image" />
                        <div class="card-body">
                            <h5 class="card-title">Coral</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="214" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="red" class="borderless-button">
                    <div class="card">
                        <img src="images/peaceful blusher/peaceful3.jpg" alt="Tan Skin Tone" class="card-img-top zoom-effect blush-image" />
                        <div class="card-body">
                            <h5 class="title">Red</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="213" />
            </div>
        </div>
    </div>
</form>


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


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>
