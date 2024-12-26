<?php
include 'mysqli.php'; 

// Fetch 4 products from subcategory_id = 6 (Lip Plummer)
$newArrivalsQuery = "
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
$newArrivalsResult = $conn->query($newArrivalsQuery);

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
    WHERE p.subcategory_id = 11
    ORDER BY p.created_at DESC
    LIMIT 3
";
$shopMoreResult = $conn->query($shopMoreQuery);

if (!$newArrivalsResult || !$shopMoreResult) {
    die("Query error: " . $conn->error);
}

// Prepare data arrays
$newArrivals = [];
$shopMore = [];

// Fetch data for New Arrivals
while ($row = $newArrivalsResult->fetch_assoc()) {
    $newArrivals[] = [
        'shade_id' => $row['shade_id'],
        'shade_name' => $row['shade_name'],
        'shade_color_code' => $row['shade_color_code'],
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'image' => $row['shade_image'] ?? '/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg',
    ];
}

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

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Home</title>

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
    <link href="homee.css" rel="stylesheet" type="text/css">

  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<!-- SLIDES -->
<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/1s.png" class="d-block w-100" alt="Banner 1">
        </div>
        <div class="carousel-item">
            <img src="images/5s.png" class="d-block w-100" alt="Banner 2">
        </div>
		 <div class="carousel-item">
            <img src="images/6s.png" class="d-block w-100" alt="Banner 2">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon"></span>
    </a>
</div>




<!-- New Arrivals Section -->
<span class="sr-only">Previous</span>
<div class="container mt-3">
    <br>
    <h1 class="newtext">New Arrivals</h1>
    <div id="new-arrivals" class="row text-center">
        <div class="col-12">
            <p class="text-right">
                <a href="all-products.php" class="btn btn-link" style="text-decoration: underline; color: black;">Shop All</a>
            </p>
        </div>
        <?php foreach ($newArrivals as $product): ?>
           <div class="col-6 col-md-4 pb-4">
               <div class="card">
                   <a href="product-details.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>">
                       <img 
                           class="card-img-top primary-image" 
                           src="<?= htmlspecialchars($product['image']); ?>" 
                           alt="<?= htmlspecialchars($product['shade_name']); ?>" 
                           loading="lazy" 
                           onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                   </a>
                   <div class="card-body">
                       <h5 class="card-title" style="color: black; font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">
                           <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
                       </h5>
                       <p class="card-text" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif; font-size: 12px;">
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


<!-- Shop Eyeshadow -->
<div class="container mt-5">
    <div class="row align-items-center">
        <div class="col-md-6">
		<div class= "shoptext">
            <h1 class="text-left">Shop the Latest Eyeshadow</h1></div>
            <p class="shonowDesc" style= "font-size: 14px;">
                Discover our exclusive range of eyeshadows that are designed to hydrate, and give you a stunning finish. Made with all-natural ingredients, Our eyeshadow products are the perfect addition to your beauty routine.
            </p>
			<div class="text-left">
<button type="button" class="shopnow" onclick="window.location.href='ProductEyes.php'">Shop Now</button>
</div>
	<br>
        </div>
        <!-- Image on the Right -->
        <div class="col-md-6">
            <img src="images/eyesdark.jpg" alt="Lip Plummer" class="img-fluid">
        </div>
    <br>
</div>

<!-- About Us Section -->
<div class="container mt-3">
    <br><br>
    <div class="row text-center mt-4">
        <div class="col-lg-6 col-md-6">
            <div class="ownerImg">
                <img class="card-img-top" src="images/ownern.jpg" alt="Owner of Classylip">
            </div>
        </div><br>
          <div class="col-lg-6 col-md-6">
     <div class="card same-size">
                <div class="aboutText">
                    <div class="aboutUs"><br>
<div style="display: flex; justify-content: center; align-items: center;">
<h1 style="font-family: 'Sorts Mill Goudy', serif; text-align: center;  ; 

">Our Story</h1>
</div>                    </div>
<p class="abousUsDesc" style="text-align: justify; padding: 10px;">
As the company's founder, I established Classylip as a local business focused on offering customers natural, safe cosmetics. Our goal at Classylip is to provide the best possible products which not only make your lips seem better but also nourish and soothe it. A combination of organic oils and butters enhances our expertly made lip balms, providing your lips with deep moisture and protection all day long.
Classylip is unique because of our constant commitment to sustainability and the use of natural, cruelty-free materials. We realize the necessity of harmless products and safe for the skin options are important especially for people with sensitive skin. 
Every application of our handmade products proves to be soothing as it is effective offering a luxurious and safe experience. As a company that values  the health of your skin, we take serious by offering high-performance makeup that is gentle on your skin.
We promise to always put your health, comfort, and confidence first. Classylip’s high-performance makeup and nourishing lip balms offer a safer, kinder alternative that aligns with a modern, conscious lifestyle.

With Classylip, you can trust that every product you use is made with love, integrity, and the highest regard for your well-being and the world we share.

</p> 
<a href="aboutus2.php" style="text-decoration: underline; display: inline-block; padding: 8px 16px; color: #333; border: none; cursor: pointer;">
  More
</a> </div> 
  
  </div>
  </div>
    </div>
</div>

<br><br><br>


<!-- Featured Products Section -->
<div class= "quiztext">
<h1 class="text-center">Makeup Quizzes</h1></div>
                    <div class="text-center d-flex justify-content-center">

<p class="quizDesc">You have got questions and you got answers. Take our quizzes to find out which products are best for you.</p>
</div><hr>

<form method="POST">
    <div class="container mt-3">
        <div class="row text-center justify-content-center">
            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="pink" class="borderless-button">
                    <div class="card">
										<a href="FindMyShade1.php">
                        <img src="images/bride blusher/bride1.jpg" alt="Fair Skin Tone" class="card-img-top zoom-effect blush-image" />
                      </a>  <div class="card-body">
                            <h5 class="card-title">Pink</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="212" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="coral" class="borderless-button">
                    <div class="card">
										<a href="FindMyShade1.php">
                        <img src="images/charisma blusher/charisma3.jpg" alt="Medium Skin Tone" class="card-img-top zoom-effect blush-image" />
                     </a>   <div class="card-body">
                            <h5 class="card-title">Coral</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="214" />
            </div>

            <div class="col-md-4 pb-3">
                <button type="submit" name="color" value="red" class="borderless-button">
                    <div class="card">
					<a href="FindMyShade1.php">
                        <img src="images/peaceful blusher/peaceful3.jpg" alt="Tan Skin Tone" class="card-img-top zoom-effect blush-image" />
                       </a> <div class="card-body">
                            <h5 class="card-title">Red</h5>
                        </div>
                    </div>
                </button>
                <input type="hidden" name="shade_id" value="213" />
            </div>
        </div>
    </div>
</form>




<br><br>


<!-- Shop More Section -->
<span class="sr-only">Previous</span>
<div class="container mt-3">
	<div class="text-center "><hr>
    <h1 class="newarrival">Shop More</h1></div><hr><br>
   <!-- Video and Product Images Section -->
<div class="d-flex justify-content-start align-items-start flex-wrap">
    <!-- Video Section --> 
<div class="col-12 col-md-3 pb-3"> 
    <div class="card">
        <iframe class="card-img-top" width="100%" height="auto" 
                src="https://www.youtube.com/embed/XcSkcHtw6eY" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
        </iframe>
        <div class="card-body">
            <h5 class="card-title">ClassyLip!</h5>
            <p class="card-text">How to Get the Look with Classylip’s Products! <br>
</p>
        </div>
    </div>
</div>


    <?php foreach ($shopMore as $product): ?>
            <!-- Product Card -->
            <div class="col-6 col-md-3 pb-3">
                <div class="card">
                    <a href="product-details.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>">
                        <img 
                            class="card-img-top" 
                            src="<?= htmlspecialchars($product['image']); ?>" 
                            alt="<?= htmlspecialchars($product['shade_name']); ?>" 
                            loading="lazy" 
                            onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title" >
                            <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
                        </h5>
                        <p class="card-text text-center"  style="color: black;    font-size: 12px; 
">
                            RM<?= number_format($product['product_price'], 2); ?>
                        </p>
						<div style="text-align: center; ">
                         <form method="POST" action="ShoppingCart.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                            <input type="hidden" name="shade_id" value="<?= htmlspecialchars($product['shade_id']); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="buttonCart">Add to Cart</button>
                        </form>
                    </div>                    </div>

                </div>
            </div>
        <?php endforeach; ?>
</div>
</div>
<br><br>
     
<!-- Subscribe Section --> 
<div class= "emailtext"> 
<h1 class="text-center" style="padding: 10px;">KEEP IN TOUCH</h1> 
    <form action="subscribe.php" method="POST"> 
    <div class="form-group" style="text-align: center;"> 
        <input  
            type="email"  
            class="form-control"  
            id="InputEmail"  
            name="email"  
            placeholder="Enter your email address"  
          
        > 
    </div> 
    <div style="text-align: center; margin-top: 10px;"> 
        <button type="submit" class="buttonSubscribe">Subscribe</button> 
    </div> 
</form> 
 
  </div>
	
<br><br>	 </div>


  <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="js/jquery-3.4.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap-4.4.1.js"></script>
</html>