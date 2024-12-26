<?php


// debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


include('mysqli.php');

// Get the product_id and shade_id from the URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$shade_id = isset($_GET['shade_id']) ? intval($_GET['shade_id']) : null;

// Validate product_id
if (!$product_id) {
    echo "Product ID is required.";
    exit();
}



    // Add to Cart 
    if (isset($_POST['quantity'])) {
        $shade_id = $_POST['shade_id'] ?? null;
        $quantity = intval($_POST['quantity']);

        // cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // if the item already exists in the cart
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['product_id'] === $product_id && $cart_item['shade_id'] === $shade_id) {
                $cart_item['quantity'] += $quantity; // Update quantity
                $item_exists = true;
                break;
            }
        }

        // If the item is new, add it to the cart
        if (!$item_exists) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'shade_id' => $shade_id,
                'quantity' => $quantity,
            ];
        }

        header("Location: product-details.php?product_id=$product_id");
        exit();
    }


// Fetch product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('MySQL prepare error (product): ' . $conn->error);
}
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "Product not found.";
    exit();
}

// Fetch shades for the product
$shade_query = "SELECT shade_id, shade_name, shade_color_code FROM product_shades WHERE product_id = ?";
$shade_stmt = $conn->prepare($shade_query);
if (!$shade_stmt) {
    die('MySQL prepare error (shades): ' . $conn->error);
}
$shade_stmt->bind_param('i', $product_id);
$shade_stmt->execute();
$shades_result = $shade_stmt->get_result();
$shades = [];
while ($shade = $shades_result->fetch_assoc()) {
    $shades[] = $shade;
}
$shade_stmt->close();

if (empty($shades)) {
    echo "No shades available for this product.";
    exit();
}

$selected_shade_id = $shade_id ?? $shades[0]['shade_id'];

// Fetch images for the selected shade
$image_query = "SELECT image_url FROM product_images WHERE shade_id = ? ORDER BY image_order ASC";
$image_stmt = $conn->prepare($image_query);
if (!$image_stmt) {
    die('MySQL prepare error (images): ' . $conn->error);
}
$image_stmt->bind_param("i", $selected_shade_id);
$image_stmt->execute();
$images_result = $image_stmt->get_result();
$images = [];
while ($row = $images_result->fetch_assoc()) {
    $images[] = $row['image_url'];
}
$image_stmt->close();

// Fetch extra information
$extra_info_query = "SELECT info FROM product_info WHERE product_id = ?";
$extra_info_stmt = $conn->prepare($extra_info_query);
if (!$extra_info_stmt) {
    die('MySQL prepare error (extra info): ' . $conn->error);
}
$extra_info_stmt->bind_param('i', $product_id);
$extra_info_stmt->execute();
$extra_info_result = $extra_info_stmt->get_result();
$extra_info_list = [];
while ($row = $extra_info_result->fetch_assoc()) {
    $extra_info_list[] = $row['info'];
}
$extra_info_stmt->close();

// Fetch FAQs for the product
$faq_query = "SELECT faq_question, faq_answer FROM faqs WHERE product_id = ?";
$faq_stmt = $conn->prepare($faq_query);
if (!$faq_stmt) {
    die('MySQL prepare error (faqs): ' . $conn->error);
}
$faq_stmt->bind_param('i', $product_id);
$faq_stmt->execute();
$faq_result = $faq_stmt->get_result();
$faqs = [];
while ($faq = $faq_result->fetch_assoc()) {
    $faqs[] = $faq;
}
$faq_stmt->close();


// Fetch Reviews for this Product
$reviews_query = "SELECT reviewer_name, star_rating, review_text, review_date 
                  FROM product_reviews WHERE product_id = ? ORDER BY review_date DESC";
$reviews_stmt = $conn->prepare($reviews_query);
if (!$reviews_stmt) {
    die('MySQL prepare error (fetch reviews): ' . $conn->error);
}
$reviews_stmt->bind_param('i', $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
}
$reviews_stmt->close();

// Exclude the current product being viewed
$current_product_id = intval($_GET['product_id']); // Ensure product_id comes from URL and is sanitized


// Fetch other products from the database
$stmt = $conn->prepare("
    SELECT 
        p.product_id, 
        p.product_name, 
        p.product_price, 
        ps.shade_id, 
        ps.shade_name, 
        pi.image_url
    FROM products p
    LEFT JOIN product_shades ps ON p.product_id = ps.product_id
    LEFT JOIN product_images pi ON ps.shade_id = pi.shade_id AND pi.image_order = 1
    WHERE p.product_id != ? 
    LIMIT 4
");
if (!$stmt) {
    die('MySQL prepare error (shop more): ' . $conn->error);
}
$stmt->bind_param("i", $current_product_id);
$stmt->execute();
$result = $stmt->get_result();

$other_products = [];
while ($row = $result->fetch_assoc()) {
    $other_products[] = $row;
}
$stmt->close();

//  close the database connection
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title ><?php echo htmlspecialchars($product['product_name']); ?></title>
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
    <link href="productdetails1.css" rel="stylesheet" type="text/css">
	
	<style>
	body, 
.panel,
.features-list	{
  font-size: 16px;
  line-height: 1.5;
font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"
}

  ShopText{
	  font-family:Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"	;
    	font-style: italic;
	color: black;
}


	</style>
</head>

    <div class="site-wrap">
           <?php include 'includes/navigation.php'; ?>

<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


  <!-- ZOOM IN AND OUT -->
      <div id="lightbox" class="lightbox">
        <span class="close" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-content">
          <img id="lightbox-image" class="lightbox-image">            
            <div class="zoom-controls">
              <button onclick="zoomIn()">+</button>
              <button onclick="zoomOut()">-</button>
            </div>
          </div>
        </div>
  
  <div class="container">
    <div class="row">
   
    <!-- Image Slider -->
    <div class="col-md-6">
      <div class="slider-container">
        <div class="slider">
          <?php foreach ($images as $image_url): ?>
            <img src="<?= htmlspecialchars($image_url) ?>" alt="Product Image" class="slide" onclick="openLightbox(this)">
          <?php endforeach; ?>
        </div>
        <button class="prev" onclick="slideLeft()">&#10094;</button>
        <button class="next" onclick="slideRight()">&#10095;</button>
      </div>
      <div class="thumbnail-container">
        <?php foreach ($images as $image_url): ?>
          <img class="thumbnail" src="<?= htmlspecialchars($image_url) ?>" alt="Thumbnail" onclick="jumpToSlide(0)">
        <?php endforeach; ?>
      </div>
    </div>


    
    <!-- Right Column for Product Details -->
      <div class="col-md-6" >
	  <div class="nameProduct">
       <h1 > 
    <?php 
    // Display the product name
    echo htmlspecialchars($product['product_name']); 

    // Check if shades are available for the product and display the selected shade name
    if (!empty($shades)) {
        $selected_shade = null;

        // Check if a shade_id is passed in the URL, otherwise use the first shade
        if ($shade_id) {
            foreach ($shades as $shade) {
                if ($shade['shade_id'] == $shade_id) {
                    $selected_shade = $shade;
                    break;
                }
            }
        }

        // Default to the first shade if no selected shade
        if (!$selected_shade) {
            $selected_shade = $shades[0];
        }

        // Display the selected shade name
        echo " in " . htmlspecialchars($selected_shade['shade_name']);
    }
    ?>
</h1>
        <h2 class="product-price">MYR <?= number_format($product['product_price'], 2) ?></h2>
        <div class="rating">
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star-o"></i>
        </div>
		
			  <!-- Available Shades -->
			<div class="Shades">
    <h5>Shades</h5>
</div>
<div class="color flex1">
    <?php foreach ($shades as $shade): ?>
        <a href="product-details.php?product_id=<?php echo $product_id; ?>&shade_id=<?php echo $shade['shade_id']; ?>">
            <span style="background-color: <?= htmlspecialchars($shade['shade_color_code']); ?>; 
                        display: inline-block; 
                        width: 30px; 
                        height: 30px; 
                        border-radius: 50%; 
                        margin-right: 10px;" 
                  class="shade"></span>
        </a>
    <?php endforeach; ?>
</div>

     
		<!-- Product Description -->
		<div class="Description">
        <h5>Description</h5></div>
		<div class="product-description" style= "font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif";
">         
<p><?= nl2br(htmlspecialchars($product['product_description'])) ?></p></div></div>
			
		 <!-- Quantity & Add to Cart -->
<form method="POST" action="ShoppingCart.php">
  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
  <?php if (!empty($shades)): ?>
    <input type="hidden" name="shade_id" value="<?= htmlspecialchars($selected_shade['shade_id']) ?>">
  <?php endif; ?>
  <input type="hidden" name="action" value="add">
  <div class="quantity-wrapper">
    <div class="quantity">
      <button type="button" class="minus" aria-label="Decrease">&minus;</button>
      <input type="number" class="input-box" name="quantity" value="1" min="1" max="10" required>
      <button type="button" class="plus" aria-label="Increase">&plus;</button>
    </div>
    <button type="submit" class="buttonCart">Add to Cart</button>
  </div>
</form>

<br>
		 
<!-- Extra Information -->
		<div class= "extraText">
        <h5>Extra Information</h5></div>
       <div class="features-list">
  <ul>
            <?php foreach ($extra_info_list as $info): ?>
              <li><?= htmlspecialchars($info) ?></li>
            <?php endforeach; ?>
          </ul>
</div><br>
<!-- Accordion for FAQs -->
<div class="accordion-container">
    <?php 
    // Check if FAQs exist for this product
    if (!empty($faqs)) {
        foreach ($faqs as $faq) {
            ?>
            <button class="accordion1">
                <?php echo htmlspecialchars($faq['faq_question']); ?>
                <span class="plus-sign">+</span>
            </button>
            <div class="panel">
                <p class="p3"><?php echo nl2br(htmlspecialchars($faq['faq_answer'])); ?></p>
            </div>
            <?php
        }
    } else {
        echo "<p>No FAQs available for this product.</p>";
    }
    ?>
</div>
          </div>
        </div>
      </div>
    </div>
<div class="row"> </div><br>
<hr>
	<div class="ShopText">
    <h1 class="Product">Shop More</h1>
</div>
<hr>
<div class="container">
    <div class="row text-center">
        <?php foreach ($other_products as $product): ?>
            <div class="col-6 col-md-3 pb-3">
                <div class="card">
                    <a href="product-details.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>">
                        <img class="card-img-top" 
                             src="<?= htmlspecialchars($product['image_url'] ?? 'images/default.jpg'); ?>" 
                             alt="<?= htmlspecialchars($product['shade_name']); ?>">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title" style="color: black; font-family: Arial, sans-serif;">
                            <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
                        </h5>
                        <p class="card-text" style="color: black; font-family: Arial, sans-serif;">
                            RM<?= number_format($product['product_price'], 2); ?>
                        </p>
                        <a href="ShoppingCart.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>" 
                           class="btnCartMore" 
                           role="button">Add to Cart</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<br>

<!-- Review Section -->
<div class="review-section">
    <hr>
    <div class="ShopText">
        <h1 class="Product">Customer Reviews</h1>
    </div>
    <hr>

    <!-- Review Button to Show Form -->
    <div class="text-center">
        <button id="show-review-form" class="btn btn-primary">Write a Review</button>
    </div>

    <div class="container">
        <!-- Review Form (Initially Hidden) -->
        <div class="row text-left" id="review-form-container" style="display: none;">
            <form id="review-form" method="POST" action="submit_review.php">
                <!-- Hidden Product ID -->
                <input type="hidden" id="product-id" name="product_id" value="<?php echo $product_id; ?>">

                <!-- Nickname Input -->
                <div class="form-group">
                    <label for="reviewer-name">Nickname:</label>
                    <input type="text" class="form-control" id="reviewer-name" name="reviewer_name" placeholder="Your Name" required>
                </div>

                <!-- Star Rating Input -->
                <div class="form-group">
                    <label for="star-rating">Rating:</label>
                    <div class="stars" id="star-rating-container">
                        <span class="star" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                    </div>
                    <input type="hidden" id="star-rating" name="star_rating" value="0">
                </div>

                <!-- Review Text Area -->
                <div class="form-group">
                    <textarea class="form-control" id="review-text" name="review_text" placeholder="Write your review..." rows="4" required></textarea>
                </div>

                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="review-list">
            <div class="review-header text-left">
                <p><strong>Total Reviews: <span id="total-reviews"><?php echo count($reviews); ?></span></strong></p>
            </div>

            <div class="review-cards" id="review-cards-container">
                <?php foreach ($reviews as $review) : ?>
                <div class="review-card">
                    <div class="review-author">
                        <strong><?php echo htmlspecialchars($review['reviewer_name']); ?></strong>
                        <span class="review-date"><?php echo date('d M Y', strtotime($review['review_date'])); ?></span>
                    </div>
                    <div class="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <span class="star <?php echo $i <= $review['star_rating'] ? 'selected' : ''; ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <div class="review-text">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary" id="load-more-reviews">Show More Reviews</button>
            </div>
        </div>
    </div>
</div>






    </div>
</div>
</div>
    </div>
	  </div>
    </div>

     <!-- Footer  -->

  <?php
include 'includes/footer.php';
?>

<!-- Script for ZOOM IN AND OUT IMAGE -->
<script>
let currentZoom = 0.5;

function openLightbox(imgElement) {
  const lightbox = document.getElementById("lightbox");
  const lightboxImage = document.getElementById("lightbox-image");
  
  lightbox.style.display = "flex"; // Show the lightbox
  lightboxImage.src = imgElement.src; // Set the clicked image source
  currentZoom = 0.5; // Reset zoom to original size
  lightboxImage.style.transform = `scale(${currentZoom})`;

  // Show zoom controls when lightbox is open
  document.querySelector('.zoom-controls').style.display = 'flex';
}

function closeLightbox() {
  document.getElementById("lightbox").style.display = "none"; 
  // Hide zoom controls when lightbox is closed
  document.querySelector('.zoom-controls').style.display = 'none';
}

function zoomIn() {
  if (currentZoom < 2) { // Limit zoom-in to 2x
    currentZoom += 0.5;
    document.getElementById("lightbox-image").style.transform = `scale(${currentZoom})`;
  }
}

function zoomOut() {
  if (currentZoom > 0.5) { // Limit zoom-out to original size (1x)
    currentZoom -= 0.5;
    document.getElementById("lightbox-image").style.transform = `scale(${currentZoom})`;
  }
}
</script>
  
  <!-- Script for Slides Imge -->
<script>
 let currentIndex = 0;
const slides = document.querySelectorAll('.slide');
const thumbnails = document.querySelectorAll('.thumbnail');
const totalSlides = slides.length;

// Function to show the current slide
function showSlide(index) {
  const offset = -index * 100; // Calculate the offset to slide
  document.querySelector('.slider').style.transform = `translateX(${offset}%)`;

  // Update the active thumbnail
  thumbnails.forEach((thumbnail, idx) => {
    if (idx === index) {
      thumbnail.classList.add('active');
    } else {
      thumbnail.classList.remove('active');
    }
  });
}

// Slide to the left
function slideLeft() {
  currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalSlides - 1;
  showSlide(currentIndex);
}

// Slide to the right
function slideRight() {
  currentIndex = (currentIndex < totalSlides - 1) ? currentIndex + 1 : 0;
  showSlide(currentIndex);
}

// Jump to a specific slide when thumbnail is clicked
function jumpToSlide(index) {
  currentIndex = index;
  showSlide(currentIndex);
}

// Initialize by showing the first slide and making the first thumbnail active
showSlide(0);
</script>

<!---SCRIPT QUANTITY CART ITEM --->
<script>
 (function () {
  const quantityContainer = document.querySelector(".quantity");
  const minusBtn = quantityContainer.querySelector(".minus");
  const plusBtn = quantityContainer.querySelector(".plus");
  const inputBox = quantityContainer.querySelector(".input-box");

  updateButtonStates();

  quantityContainer.addEventListener("click", handleButtonClick);
  inputBox.addEventListener("input", handleQuantityChange);

  function updateButtonStates() {
    const value = parseInt(inputBox.value);
    minusBtn.disabled = value <= 1;
    plusBtn.disabled = value >= parseInt(inputBox.max);
  }

  function handleButtonClick(event) {
    if (event.target.classList.contains("minus")) {
      decreaseValue();
    } else if (event.target.classList.contains("plus")) {
      increaseValue();
    }
  }

  function decreaseValue() {
    let value = parseInt(inputBox.value);
    value = isNaN(value) ? 1 : Math.max(value - 1, 1);
    inputBox.value = value;
    updateButtonStates();
    handleQuantityChange();
  }

  function increaseValue() {
    let value = parseInt(inputBox.value);
    value = isNaN(value) ? 1 : Math.min(value + 1, parseInt(inputBox.max));
    inputBox.value = value;
    updateButtonStates();
    handleQuantityChange();
  }

  function handleQuantityChange() {
    let value = parseInt(inputBox.value);
    value = isNaN(value) ? 1 : value;

    // Execute code based on the updated quantity value
    console.log("Quantity changed:", value);
  }
})();

</script>
		 

<!----SCRIPT BUTTON TEXT DETIAILS--->
<script>
var acc = document.getElementsByClassName("accordion1");
var i;

// Initially, set all panels to be closed when the page loads
for (i = 0; i < acc.length; i++) {
  var panel = acc[i].nextElementSibling;
  panel.style.maxHeight = "0"; // Make sure panel is initially closed
  panel.style.transition = "max-height 0.3s ease-out"; // Smooth transition for max-height

  acc[i].addEventListener("click", function() {
    var panel = this.nextElementSibling;
    var plusSign = this.querySelector(".plus-sign");

    // Toggle between open and closed state
    if (panel.style.maxHeight && panel.style.maxHeight !== "0px") {
      panel.style.maxHeight = "0"; // Close the panel
      plusSign.textContent = "+"; // Change plus sign when panel is closed
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px"; // Open the panel
      plusSign.textContent = "-"; // Change plus sign when panel is opened
    }
  });
}

</script>

<!-- Review Form Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const showReviewFormButton = document.getElementById("show-review-form");
    const reviewFormContainer = document.getElementById("review-form-container");

    // Toggle the form visibility when the button is clicked
    showReviewFormButton.addEventListener("click", function() {
        if (reviewFormContainer.style.display === "none") {
            reviewFormContainer.style.display = "block";
            showReviewFormButton.innerText = "Cancel Review";
        } else {
            reviewFormContainer.style.display = "none";
            showReviewFormButton.innerText = "Write a Review";
        }
    });

    // Add click event for rating stars
    document.querySelectorAll('.star').forEach(function(star) {
        star.addEventListener('click', function() {
            const ratingValue = parseInt(this.getAttribute('data-value'), 10);
            document.getElementById('star-rating').value = ratingValue;

            const stars = document.querySelectorAll('.star');
            stars.forEach(function(s) {
                s.classList.remove('selected');
            });

            for (let i = 0; i < ratingValue; i++) {
                stars[i].classList.add('selected');
            }
        });
    });

    // Handle review form submission
    document.getElementById('review-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const reviewerName = document.getElementById('reviewer-name').value.trim();
        const reviewText = document.getElementById('review-text').value.trim();
        const starRating = document.getElementById('star-rating').value;
        const productId = document.getElementById('product-id').value.trim(); // Add product ID here

        // Validate the form inputs
        if (!reviewerName || !reviewText || starRating === "0" || !productId) {
            alert("Please fill in all fields and select a rating.");
            return;
        }

        // Send review data to PHP script
        const reviewData = new FormData();
        reviewData.append('reviewer_name', reviewerName);
        reviewData.append('review_text', reviewText);
        reviewData.append('star_rating', starRating);
        reviewData.append('product_id', productId); // Send product_id as well

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_review.php', true);

        // Add console log to check the response
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log(xhr.responseText);  // Log the response to console for debugging
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Review submitted successfully!");

                        // Create a new review card dynamically
                        const newReviewCard = document.createElement('div');
                        newReviewCard.classList.add('review-card');
                        newReviewCard.innerHTML = `
                            <div class="review-author">
                                <strong>${reviewerName}</strong>
                                <span class="review-date">${new Date().toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                            </div>
                            <div class="star-rating">
                                ${'★'.repeat(starRating)}${'☆'.repeat(5 - starRating)}
                            </div>
                            <div class="review-text">${reviewText}</div>
                        `;

                        // Prepend the new review card to the review list
                        document.getElementById('review-cards-container').prepend(newReviewCard);

                        // Reset the form after submission
                        document.getElementById('review-form').reset();
                        document.getElementById('star-rating').value = "0";

                        // Reset stars UI
                        document.querySelectorAll('.star').forEach(function(star) {
                            star.classList.remove('selected');
                        });
                    } else {
                        alert("Error: " + response.message);
                    }
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    alert("An error occurred. Please try again.");
                }
            } else {
                alert("An error occurred. Please try again.");
            }
        };

        xhr.onerror = function() {
            alert("An error occurred. Please try again.");
        };

        xhr.send(reviewData);
    });
});
</script>
<!----SCRIPT BUTTON READ MORE REVIEW--->
<script>
// Initially, display only the first 3 reviews
document.addEventListener('DOMContentLoaded', function() {
    let reviews = document.querySelectorAll('.review-card');
    reviews.forEach(function(review, index) {
        if (index >= 3) {
            review.style.display = 'none'; // Hide reviews after the 3rd
        }
    });
});

// Toggle visibility of reviews when "Read More" button is clicked
document.getElementById('load-more-reviews').addEventListener('click', function() {
    let reviews = document.querySelectorAll('.review-card');
    reviews.forEach(function(review) {
        review.style.display = 'block'; // Show all reviews
    });

    // Hide the "Read More" button after it's clicked
    this.style.display = 'none';

    // Update the total review count
    document.getElementById('total-reviews').innerText = reviews.length;
});

</script>
<!-- Total Reviews Count Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to count reviews
    function updateReviewCount() {
        let reviews = document.querySelectorAll('.review-card');
        let totalReviews = reviews.length;
        document.getElementById('total-reviews').innerText = totalReviews;
    }

    // Initially, count and display the total number of reviews
    updateReviewCount();

    // Handle "Read More" button click
    document.getElementById('load-more-reviews').addEventListener('click', function() {
        let reviews = document.querySelectorAll('.review-card');
        reviews.forEach(function(review) {
            review.style.display = 'block'; // Show all reviews
        });

        // Hide the "Read More" button after it's clicked
        this.style.display = 'none';

        // Update the review count after all reviews are visible
        updateReviewCount();
    });
});
</script>






<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="js/jquery-3.4.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap-4.4.1.js"></script>
  
  

</body>
</html>