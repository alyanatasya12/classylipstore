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
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >About Us</title>
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
    <link href="homee.css" rel="stylesheet" type="text/css">
	
	 

</head>
  <body>
  <style>
  .emailtext {
  font-family: 'Sorts Mill Goudy', serif;  
	color: #A55548 ;

}


/* Input email Styling */
#InputEmail {
    font-size: 14px; 
    padding: 10px; 
    border: 1px solid #ccc; 
    border-radius: 5px; 
    width: 100%; 
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); 
    transition: border-color 0.3s ease, box-shadow 0.3s ease; 
}

#InputEmail:focus {
    border-color: #ff69b4; 
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
    outline: none; 
}

/* Helper Text Styling */
#emailHelp1 {
    font-size: 12px; 
    color: #6c757d; 
    margin-top: 5px; 
    line-height: 1.5; 
}
.buttonSubscribe {
    background-color: white;
    border: 2px solid black;
    border-radius: 24px;
    padding: 8px 16px;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.5px;
    color: black;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
    display: inline-block;
}

.buttonSubscribe:hover,
.buttonSubscribe:focus {
    background-color: #A55548;
    color: white;
    outline: none;
}
.form-control {
  height: 43px; }
  .form-control:active, .form-control:focus {
    border-color: #FFFFFF; }
  .form-control:hover, .form-control:active, .form-control:focus {
    -webkit-box-shadow: none !important;
    box-shadow: none !important; }

  </style>
 
  
           <?php include 'includes/navigation.php'; ?>

<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<br><br>
	   <section class="client">
       <div class="container">
<div class="row" style="align-items: flex-center;">    <!-- Left column for the image -->
    <div class="col-lg-6 col-md-6">
      <img src="images/ownern.jpg" alt="Image" class="img-fluid">
    </div>
<br>
    <!-- Right column for the text -->
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
</div> 
  
  </div>
  </div>
</div>

    </section>
	  <br>  <br>
	  <div class="container-fluid4">
  <div class="container4"><img src="images/6s.png" class="rounded img-fluid" alt="Placeholder image"></div>
  </div>
	  <br>	  <br>

	  <div class="container5">
  <div class="row justify-content-center">
    <div class="col-lg-8 text-center">
	<hr>
<div  class= "homemade"style="display: flex; justify-content: center; align-items: center;">
    <h1 style="font-family: 'Sorts Mill Goudy', serif;  text-align: center;    color: #A55548;  
   ">Homemade Products</h1>
</div><hr>

      <p>At Classylip, we offer a range of homemade lip care products crafted with meticulous attention to detail and a deep understanding of each ingredient's benefits. Our formulations are based on extensive research to ensure that only the purest natural ingredients, such as organic oils, butters, and plant-based extracts, are used. This careful selection guarantees that each product not only enhances your lips' natural beauty but also delivers deep hydration, soothing relief, and long-lasting protection.

We understand that everyone’s skin is unique, which is why our products are designed to be universal and gentle, making them suitable for all skin types, including sensitive and dry skin. Our commitment to quality and safety means that every balm, butter, or gloss is free from harmful chemicals, parabens, and synthetic additives. Instead, we rely on nature’s finest elements like shea butter, cocoa butter, coconut oil, and vitamin E to nourish and restore your lips..</p>
      <p>To further ensure the safety and efficacy of our products, all our formulations are dermatologist-approved and undergo rigorous quality checks. This ensures that every product delivers a luxurious, comforting experience with every application. At Classylip, our mission is to combine the art of homemade craftsmanship with the science of skincare, offering you natural, sustainable, and ethically-made products that leave your lips feeling soft, healthy, and beautifully cared for.

Indulge in the Classylip experience where every swipe brings you closer to nature’s best, wrapped in luxury and care.</p>
    </div>
  </div>
</div>

	  <br>
<hr>
<div style="display: flex; justify-content: center; align-items: center;">
    <h1  class="shopmore" style="font-family: 'Sorts Mill Goudy', serif;  text-align: center;     color: #A55548;  

">Shop More</h1>
</div>
<hr>

<div class="container mt-3">
 <div class="row">

      
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
      
        </a>
        <div class="card-body">
            <h5 class="card-title" style=" color: black; font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">
                <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
            </h5>
            <p class="card-text" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;    font-size: 12px; text-align: center;
">
                RM<?= number_format($product['product_price'], 2); ?>
            </p>
           <form method="POST" action="ShoppingCart.php" style="text-align: center;">
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

<br><br>


<!-- Subscribe Section --> 
<div class="subscribe-section text-center">
<h1 class="text-center" style="  font-family: 'Sorts Mill Goudy', serif; color: #A55548 ;
">KEEP IN TOUCH</h1> 
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
  <?php
include 'includes/footer.php';
?>

	
 <!----footer section design--->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>
	    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
	  
	  

  </body>
</html>
