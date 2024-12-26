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

// Check if the form is submitted 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['color_lips'])) {
    $color_lips = $_POST['color_lips'];
    switch ($color_lips) {
        case 'reds and plums':
            $shade_id = 215; // Rough (Red Plums)
            break;
        case 'nudes and pinks':
            $shade_id = 219; // Blush (Nudes and Pinks)
            break;
        case 'mauves and berries':
            $shade_id = 218; // Rosewood (Mauves and Berries)
            break;
        default:
            $shade_id = null; 
    }

    // Save the user's selection in the session
    $_SESSION['question2'] = [
        'product_id' => 189, 
        'shade_id' => $shade_id,
        'color_lips' => $color_lips
    ];

    // Debugging: print session to check if the value is stored correctly
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    // Redirect to the next question
    header("Location: FindMyShade3.php");
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
  <div class="quiztext">
        <h2 class="QuizHeader">Choose your go-to lip color </h2>
		                    <div class="text-center d-flex justify-content-center">
        <p class="Description" style="font-size: 16px">  Select your favorite lip color and vibe with us! Let us help you find the perfect shade!</p>
    </div>   </div>
   

   <form method="POST">
    <div class="container mt-3">
        <div class="row text-center justify-content-center">
            <div class="col-md-4 pb-3">
                <button type="submit" name="color_lips" value="reds and plums" class="borderless-button" style="border: none; background: none;">
                    <div class="card">
                        <img src="images/rough plum/rough2.jpg" alt="Reds and plums" class="card-img-top zoom-effect blush-image" width="300" height="300" />
                        <div class="card-body">
                            <h5 class="card-title">Reds and plums</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="215" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color_lips" value="nudes and pinks" class="borderless-button" style="border: none; background: none;">
                    <div class="card">
                        <img src="images/blush plum/blush1.jpg" alt="Nudes and pinks" class="card-img-top zoom-effect blush-image" width="300" height="300" />
                        <div class="card-body">
                            <h5 class="card-title">Nudes and pinks</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="219" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color_lips" value="mauves and berries" class="borderless-button" style="border: none; background: none;">
                    <div class="card">
                        <img src="images/Lipmatte charm/charm3.jpg" alt="Mauves and berries" class="card-img-top zoom-effect blush-image" width="300" height="300" />
                        <div class="card-body">
                            <h5 class="card-title">Mauves and berries</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="218" />
            </div>
        </div>
    </div>
</form>

	
	
<div class="container mt-3">
 <div class="row"></div>
<hr>
<h1 class="Product" style="font-family: 'Sorts Mill Goudy', serif;    color: #A55548;  
">Shop More</h1>
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
    <hr>
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