<?php
session_start();
include ('mysqli.php');
// Fetch all products from db
$query = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_price,
        p.product_description,
        ps.shade_id,
        ps.shade_name,
        ps.shade_color_code,
        (
            SELECT pi.image_url 
            FROM product_images pi 
            WHERE pi.product_id = p.product_id AND pi.shade_id = ps.shade_id 
            ORDER BY pi.image_order ASC 
            LIMIT 1
        ) AS product_image
    FROM products p
    LEFT JOIN product_shades ps ON p.product_id = ps.product_id

";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("MySQL prepare error: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'product_description' => $row['product_description'],
        'shade_id' => $row['shade_id'],
        'shade_name' => $row['shade_name'],
        'shade_color_code' => $row['shade_color_code'],
        'image' => $row['product_image'] ?: '/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg',
    ];
}

// Close connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Shop All</title>

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
    <link href="allproduct1.css" rel="stylesheet" type="text/css"> <!-- Custom CSS -->


<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

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
<br>



<!-- Shop All Section -->
<div class="container mt-3">
<div class="text-center" style=" justify-content: center; align-items: center;  text-align: center;">
    <h1 style="font-size: 36px; font-family: 'Sorts Mill Goudy', serif;color: #A55548;  text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); ">
        Shop All
    </h1>
</div>	</div>


<div class="text-center">
        <p class="mt-2">ClassyLips has everything you need for a flawless face, offering high-quality products for eyes, cheeks, and lips.</p>
    </div>


<div id="new-arrivals" class="container">
    <?php foreach (array_chunk($products, 4) as $row): ?>
        <div class="row justify-content-center text-center">
            <?php foreach ($row as $product): ?>
                <!-- Product Card -->
                <div class="col-6 col-md-3 pb-3">
                    <div class="card">
                        <a href="product-details.php?product_id=<?= htmlspecialchars($product['product_id']); ?>&shade_id=<?= htmlspecialchars($product['shade_id']); ?>">
                            <div class="product-image-wrapper">
                                <img 
                                    class="card-img-top primary-image" 
                                    src="<?= htmlspecialchars($product['image']); ?>" 
                                    alt="<?= htmlspecialchars($product['shade_name']); ?>" 
                                    loading="lazy" 
                                    onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                            </div>
                        </a>
                        <div class="card-body">
                            <h5 class="card-title" style="color: black;">
                                <?= htmlspecialchars($product['product_name']); ?> <br> in <?= htmlspecialchars($product['shade_name']); ?>
                            </h5>
                            <p class="card-text" style="color: black;">
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
    <?php endforeach; ?>
</div>




<!-- QUIZ -->
<div class="container mt-5">
    <div class="row align-items-center">
        <!-- Text on the Left -->
        <div class="col-md-6">
            <h2 class="text-left" style="font-family: 'Sorts Mill Goudy', serif;     color: #A55548;  
">Find Your Perfect Beauty Matches</h2>
            <p class="text-left">
                Explore personalized beauty solutions just for you. 
                Take our quizzes to find your perfect match!
            </p>
            <div class="text-left">
                <button type="button" class="buttonquiz" onclick="window.location.href='FindMyShade1.php'">Take Quiz</button>
            </div><br>
        </div>
        
        <!-- Image on the Right -->
        <div class="col-md-6">
            <img src="images/7.png" alt="Lip Plumper" class="img-fluid">
        </div>
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
	
  <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   


<!--- SCRIPT FOR SORT BY BUTTON---->
<script>

function toggleSortOptions() {
    const sortOptions = document.getElementById('sortOptions');
    const arrow = document.querySelector('.arrow');

    // Toggle dropdown visibility
    sortOptions.classList.toggle('d-none');
    
    // Toggle arrow rotation
    arrow.classList.toggle('open');
}

// Close the dropdown if clicked outside
document.addEventListener('click', function (event) {
    const sortByButton = document.getElementById('sortByButton');
    const sortOptions = document.getElementById('sortOptions');

    if (!sortByButton.contains(event.target) && !sortOptions.contains(event.target)) {
        sortOptions.classList.add('d-none');
        document.querySelector('.arrow').classList.remove('open');
    }
});

// Handle sort option clicks
document.querySelectorAll('.sort-option').forEach(option => {
    option.addEventListener('click', function (e) {
        e.preventDefault();
        const sortByButton = document.getElementById('sortByButton');
        sortByButton.innerHTML = `Sort By: ${this.textContent.trim()} <span class="arrow">&#9662;</span>`;
        const sortType = this.dataset.sort;

        // Close dropdown and reset arrow
        document.getElementById('sortOptions').classList.add('d-none');
        document.querySelector('.arrow').classList.remove('open');

        // Placeholder for sorting logic
        console.log(`Sorting by: ${sortType}`);
    });
});
</script>

<!--- SCRIPT FOR TYPES BUTTON---->
<script>
// Toggle Sort By Options
function toggleTypeOptions() {
    const typeOptions = document.getElementById('typeOptions');
    const arrow = document.querySelector('#typesButton .arrow');

    // Toggle dropdown visibility
    typeOptions.classList.toggle('d-none');

    // Toggle arrow rotation
    arrow.classList.toggle('open');
}

// Close the dropdown if clicked outside
document.addEventListener('click', function (event) {
    const typesButton = document.getElementById('typesButton');
    const typeOptions = document.getElementById('typeOptions');

    if (!typesButton.contains(event.target) && !typeOptions.contains(event.target)) {
        typeOptions.classList.add('d-none');
        document.querySelector('#typesButton .arrow').classList.remove('open');
    }
});

// Handle type option clicks
document.querySelectorAll('.type-option').forEach(option => {
    option.addEventListener('click', function (e) {
        e.preventDefault();
        const typesButton = document.getElementById('typesButton');
        typesButton.innerHTML = `Types: ${this.textContent.trim()} <span class="arrow">&#9662;</span>`;
        const type = this.dataset.type;

        // Close dropdown and reset arrow
        document.getElementById('typeOptions').classList.add('d-none');
        document.querySelector('#typesButton .arrow').classList.remove('open');

        // Placeholder for filtering logic
        console.log(`Filtering by type: ${type}`);
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