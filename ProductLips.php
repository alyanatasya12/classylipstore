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
$recordsPerPage = 9; // Shades per page
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

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >Lips</title>
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
    <link href="ProductCheeks.css" rel="stylesheet" type="text/css">

</head>
  <body>
 
 <div class="site-wrap">
           <?php include 'includes/navigation.php'; ?>

<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	  
	  
	  
	  <div class="col-12">
      <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#" data-slide-to="0" class="active"></li>
          <li data-target="#" data-slide-to="1"></li>
          <li data-target="#" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active"> <img class="d-block w-100" src="images/cheecks.png" alt="First slide">
            <div class="carousel-caption d-none d-md-block"></div>
          </div>
          <div class="carousel-item"> <img src="images/lips.png" alt="Second slide"  class="d-block w-100"> </div>
          <div class="carousel-item"> <img class="d-block w-100" src="images/eyes.png"  alt="Third slide"> </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span> </a> </div>
    </div>
    <div class="container mt-3">
      <div class="row"> </div>
      <hr>
  </div> 
    <hr>
<div class="lips">
    <h2>Lips&nbsp;</h2>
    <label for="sort">Sort by:&nbsp;</label>
    <select id="sort" onchange="sortItems()">
        <option value="latest" <?php echo ($sort === 'latest') ? 'selected' : ''; ?>>Latest</option>
        <option value="low-to-high" <?php echo ($sort === 'low-to-high') ? 'selected' : ''; ?>>Low to high</option>
        <option value="high-to-low" <?php echo ($sort === 'high-to-low') ? 'selected' : ''; ?>>High to low</option>
    </select>
	
</div>
<hr>
<div class="container">
    <div class="row">
        <?php foreach ($shades as $shade): ?>
            
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <a href="product-details.php?product_id=<?= htmlspecialchars($shade['product_id']); ?>">
                         <a href="product-details.php?product_id=<?= htmlspecialchars($shade['product_id']); ?>&shade_id=<?= htmlspecialchars($shade['shade_id']); ?>">
                          <img 
                              src="<?= htmlspecialchars($shade['image']); ?>" 
                              alt="<?= htmlspecialchars($shade['product_name'] . ' - ' . $shade['shade_name']); ?>" 
                              class="card-img-top" 
                              loading="lazy" 
                              onerror="this.onerror=null; this.src='/ClassylipWebsite/Website/ClassyLipWebsite/admin/productimages/uploads/default-image.jpg';">
                            </a>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($shade['product_name']); ?></h5>
                        <p class="card-text"><?= htmlspecialchars($shade['shade_name']); ?></p>
                        <p class="card-text">MYR <?= htmlspecialchars(number_format($shade['product_price'], 2)); ?></p>
                        <a href="#" data-product-id="<?= $shade['product_id']; ?>" class="btn btn-primary add-to-cart">Add to Cart</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>







</div>

<br>
<div class="row text-center">
    <div class="container">
        <img src="images/quiz_cheeks.png" width="1920" height="530" class="img-fluid" alt="Quiz Image">
    </div>
</div>
<br>
<!-- Pagination HTML -->
<div class="buttonpage-container">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <button class="buttonpage-button" onclick="goToPage(<?= $i; ?>)"><?= $i; ?></button>
    <?php endfor; ?>
</div>

<script>
function goToPage(page) {
    window.location.href = `?page=${page}`;
}
</script>
<br>

<?php
include 'includes/footer.php';
?>


<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const productId = this.dataset.productId;

        fetch(`ShoppingCart.php?product_id=${productId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
            } else {
                alert('Failed to add product to cart.');
            }
        });
    });
});

</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= htmlspecialchars($shade['product_name']); ?>",
    "description": "<?= htmlspecialchars($shade['shade_name']); ?>",
    "image": "<?= htmlspecialchars($shade['image']); ?>",
    "offers": {
        "@type": "Offer",
        "priceCurrency": "MYR",
        "price": "<?= htmlspecialchars($shade['product_price']); ?>"
    }
}
</script>

<script>
function sortItems() {
    var sortValue = document.getElementById("sort").value;
    var itemsContainer = document.querySelector('.row.text-center');
    var items = Array.from(itemsContainer.getElementsByClassName('product-item'));

    items.sort(function(a, b) {
        var priceA = parseFloat(a.querySelector('.card-text').innerText.replace('RM', '').trim());
        var priceB = parseFloat(b.querySelector('.card-text').innerText.replace('RM', '').trim());

        if (sortValue == 'low-to-high') {
            return priceA - priceB;
        } else if (sortValue == 'high-to-low') {
            return priceB - priceA;
        } else {
            // For "Latest", you can add sorting logic by date or ID (customize as needed)
            return 0; // Default sorting, no change
        }
    });

    // Reorder the items based on the sort
    items.forEach(function(item) {
        itemsContainer.appendChild(item);
    });
}

</script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>