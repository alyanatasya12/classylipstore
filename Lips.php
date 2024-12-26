<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classylip";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$recordsPerPage = 15; // Shades per page
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

// Sort parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest'; // Default to 'latest' sorting
$orderClause = '';

if ($sort === 'low-to-high') {
    $orderClause = 'ORDER BY p.product_price ASC';
} elseif ($sort === 'high-to-low') {
    $orderClause = 'ORDER BY p.product_price DESC';
} else {
    $orderClause = 'ORDER BY p.created_at DESC'; // Default sorting
}

// Fetch total records for pagination
$totalQuery = "
    SELECT COUNT(DISTINCT ps.shade_id) AS total
    FROM product_shades ps
    INNER JOIN products p ON ps.product_id = p.product_id
    INNER JOIN subcategory s ON p.subcategory_id = s.subcategory_id
    INNER JOIN category c ON s.category_id = c.category_id
    WHERE c.category_id = 15
";
$totalResult = $conn->query($totalQuery);

if ($totalResult) {
    $totalRecords = $totalResult->fetch_assoc()['total'];
} else {
    die("Error fetching total records: " . $conn->error);
}

$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch shades with sorting
$query = "
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
    INNER JOIN subcategory s ON p.subcategory_id = s.subcategory_id
    INNER JOIN category c ON s.category_id = c.category_id
    WHERE c.category_id = 15
    $orderClause
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("MySQL prepare error: " . $conn->error);
}
$stmt->bind_param("ii", $recordsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$shades = [];
while ($row = $result->fetch_assoc()) {
    $shades[] = [
        'shade_id' => $row['shade_id'],
        'shade_name' => $row['shade_name'],
        'shade_color_code' => $row['shade_color_code'],
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'image' => $row['shade_image'] ?? '/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg',
    ];
}


// Fetch images for the shades
if (!empty($shades)) {
    $shadeIds = implode(',', array_keys($shades)); // Gather all the shade_ids
    $imageQuery = "
        SELECT pi.image_url, ps.shade_id
        FROM product_images pi
        INNER JOIN product_shades ps ON pi.shade_id = ps.shade_id
        WHERE ps.shade_id IN ($shadeIds)
        ORDER BY pi.image_order = 1
    ";
    $imageResult = $conn->query($imageQuery);

    if ($imageResult) {
        while ($imageRow = $imageResult->fetch_assoc()) {
            $shadeId = $imageRow['shade_id'];
            if (isset($shades[$shadeId])) {
                // Use the image URL as is from the database
                $shades[$shadeId]['image'] = $imageRow['image_url'];
            }
        }
    } else {
        die("Error fetching images: " . $conn->error);
    }
}

$stmt->close();
$conn->close();
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Lips</title>

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
    <link href="eye.css" rel="stylesheet" type="text/css"> <!-- Custom CSS -->


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
            <img src="images/4s.png" class="d-block w-100" alt="Banner 1">
        </div>
       
</div>
<br>



<!-- New Arrivals Section -->
<div class="container mt-3">
<div class="text-center" style="display: flex; justify-content: center; align-items: center;  text-align: center;">
    <h1 style=" font-family: 'Sorts Mill Goudy', serif;font-size: 36px; color: #A55548;  text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); ">
Lips    </h1>
</div>     
<div class="text-center">
        <p class="mt-2">Discover our exclusive collection of lip products, crafted to deliver vibrant colors, long-lasting wear, and a touch of elegance to enhance every smile.</p>
    </div>
  </div>
	
	
<div id="new-arrivals" class="container">
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
                            <p class="card-text" style="color: black; ">
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
</div>



<!-- QUIZ -->
<div class="container mt-5">
    <div class="row align-items-center">
        <!-- Text on the Left -->
        <div class="col-md-6">
            <h2 class="text-left">Find Your Perfect Beauty Matches</h2>
            <p class="text-left">
                   Explore personalized beauty solutions just for you. 
				   Take our quizzes to find your perfect match!
            </p>
				   <div class="text-left">
    <button type="button" class="buttonquiz" onclick="window.location.href='FindMyShade1.php'">Take Quiz</button>
</div>
	<br>
        </div>
        <!-- Image on the Right -->
        <div class="col-md-6">
            <img src="images/Eyess.jpg" alt="Lip Plummer" class="img-fluid">
        </div>
    <br>
</div>


<br><br><br>


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
</body>
</html>