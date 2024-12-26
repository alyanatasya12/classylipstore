<?php
// Start the session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>



<!DOCTYPE html>

		<style>
		/* Mobile-specific styles */
		.container1 {
		  width: 100%;
		  padding: 10px;
		}


		/* Mobile-specific menu styles */
		.site-mobile-menu .site-nav-wrap > li > a {
		  font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
		  font-size: 16px;
		  text-transform: uppercase;
		  color: #333; 
		}

		/* Parent menu item styling */
		.site-navigation .site-menu > li > a {
		font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
		font-size: 16px;
		color: #25262a; 
		text-transform: uppercase;
		padding: 10px 15px;
		transition: color 0.3s;
		  border-radius: 8px; 

	}

	/* Dropdown styling */
	.site-navigation .site-menu .dropdown {
		background-color: #FFD9DE;
		padding: 10px 0;
		position: absolute;
		top: 100%;
		left: 0;
		border-radius: 8px; 
		display: none;
		z-index: 10;
	}

	/* Child dropdown menu items */
		.site-navigation .site-menu .dropdown li a {
		font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
		font-size: 16px;
		color: #25262a; 
		background-color: #FFD9DE;
		text-decoration: none;
		padding: 8px 20px;
		display: block;
				  border-radius: 8px; 
		transition: background 0.3s, color 0.3s;
	}


	/* Show dropdown on hover for parent menu */
	.site-navigation .site-menu .has-children:hover .dropdown {
		display: block;
		 background-color: #FFD9DE;
		 		  border-radius: 8px; 

		
	}

	/* Ensure nested dropdowns are consistent */
	.site-navigation .site-menu .dropdown .dropdown {
		top: 0;
		left: 100%;
		margin-left: 5px;
	}

	.site-navigation .site-menu .dropdown .dropdown li a {
		color: #25262a; /* Same color as all menu items */
	}

		/* Mobile-specific menu styles */
		.site-navbar .site-navigation .site-menu .has-children > a {
		  position: relative;
		  padding-right: 30px;
		}

		.site-navbar .site-navigation .site-menu .has-children .dropdown {
		  visibility: hidden;
		  opacity: 0;
		  top: 100%;
		  position: absolute;
		  text-align: left;
		  padding: 0;
		  margin-top: 20px;
		  background-color: #FFD9DE;
		  transition: opacity 0.2s ease, margin-top 0.2s ease;
		  box-shadow: 0 0px 4px rgba(0, 0, 0, 0.05);
		}
		
			/* Hover effect for dropdown items - only change font color */
		.site-navigation .site-menu .dropdown li a:hover {
		background-color:  #FFD9DE !important; 
		color: #A55548 !important; 
	}

		/* Hide desktop navigation on mobile */
		@media (max-width: 991px) { 
		  .site-navigation {
			display: none; 
		  }
		  .mobile-menu {
			display: block; 
		  }
		}

		/* Default state for mobile menu to be hidden on desktop */
		.mobile-menu {
		  display: none;
		}

		.site-navigation .site-menu > li {
		  position: relative;
		  margin-right: 20px;
		}

		.site-navigation .site-menu .dropdown {
		  display: none;
		  position: absolute;
		  top: 100%;
		  left: 0;
		  background: #FFD9DE;
		  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
		  padding: 10px 0;
		}


		.site-navigation .site-menu .dropdown li {
		  width: 180px;
		  text-align: left;
		}

				/* Parent menu item styling */
			.site-navigation .site-menu > li > a {
			font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
			font-size: 16px;
			color: #25262a; 
			text-transform: uppercase;
			padding: 10px 15px;
			transition: color 0.3s ease;
		}


		/* Remove bullet points and reset list styles */
		.mobile-menu ul, .mobile-menu .dropdown {
		  list-style-type: none;
		  padding: 0;
		  margin: 0;
		}

		/* Hide all dropdowns initially */
		.mobile-menu .dropdown {
		  display: none;
		}

		/* Style parent links with dropdowns */
		.mobile-menu .site-menu > li {
		  position: relative;
		  color: #333;
		}

		.mobile-menu a {
		  text-decoration: none;
		  color: #333;
		  display: block;
		  padding: 8px 15px;
		}

		/* Arrow or indicator for items with submenus */
		.mobile-menu .has-children > a::after {
		  content: " ▼";
		  font-size: 10px;
		  margin-left: 5px;
		}

		.mobile-menu .dropdown.show {
		  display: block;
		}

		/* Mobile Menu Container */
		.mobile-menu {
		  background-color: #FFD9DE;
		  position: relative;
		  top: 0;
		  left: 0;
		  width: 100%;
		  padding: 20px;
		  z-index: 9999;
		  border-top: 1px solid #FFD9DE;

		}

		/* Menu List Styling */
		.mobile-menu ul.site-menu {
		  list-style-type: none;
		  padding-left: 0;
		  margin: 0;
		  font-size: 1rem;
		  font-weight: bold;
		  line-height: 1.5;
		}

		.mobile-menu ul.site-menu li {
		  margin: 10px 0;
		  
		}

		.mobile-menu ul.site-menu li a {
		  color: #333;
		  text-decoration: none;
		  font-size: 1rem; 
		  font-weight: 400;
		  padding: 5px 0;
		  display: block;
		  background-color: #FFD9DE;
		  transition: color 0.3s;
		  font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
		}

		.site-footer {
		  padding: 4em 0;
		}

		.site-footer ul li {
		  margin-bottom: 10px;
		}

		.site-footer ul li a {
		  color: #5c626e;
		}

		.site-footer ul li a:hover {
		  color: #ee4266;
		}

		.site-footer .footer-heading {
		  font-size: 14px;
		  color: #25262a;
		  letter-spacing: .2em;
		  text-transform: uppercase;
		}
		
		.bx-log-out {
	font-size: 24px; 
}

.logo {
    margin-left: 20px; /* Adjusts spacing from the left */
}
		</style>
	
</head>
  <body>
 
 <!-- Linking external fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,700"> 
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
</head>

<body>
    <div class="site-wrap">
<!-- Desktop Navigation Bar -->
<div class="site-navbar py-2">
    <div class="container1">
        <div class="d-flex align-items-center justify-content-between">
            <div class="logo">
                <a href="home.php">
                    <div class="site-logo">
                        <img src="images/logonew.png" alt="Logo" width="120" height="145" class="img-fluid">
                    </div>
                </a>
            </div> 

            <!-- Desktop Navigation Menu -->
            <nav class="site-navigation">
                <ul class="site-menu">
                    <li><a href="home.php">HOME</a></li>
                    <li class="has-children">
                        <a href="all-products.php">SHOP</a>
                        <ul class="dropdown">
                            <li class="has-children">
                                <a href="ProductEyes.php">EYES</a>
                                <ul class="dropdown">
                                    <li><a href="ProductEyes.php">EYESHADOW</a></li>
                                </ul>
                            </li>
                            <li class="has-children">
                                <a href="ProductBlusher.php">CHEEKS</a>
                                <ul class="dropdown">
                                    <li><a href="ProductBlusher.php">BLUSH</a></li>
                                </ul>
                            </li>
                            <li class="has-children">
                                <a href="Lips.php">LIPS</a>
                                <ul class="dropdown">
                                    <li><a href="LipPlummer.php">LIP PLUMMER</a></li>
                                    <li><a href="LipMatte.php">LIP MATTE</a></li>
                                    <li><a href="LipCream.php">LIP CREAM</a></li>
                                    <li><a href="LipTint.php">LIP TINT</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="aboutus2.php">ABOUT US</a></li>
                    <li><a href="FindMyShade1.php">FIND MY SHADE</a></li>
                </ul>
            </nav>

            <!-- Icons -->
            <div class="icons">
                <a href="#" class="icons-btn d-inline-block js-search-open" id="searchToggle">
                    <span class="bx bx-search" id="searchIcon"></span>
                </a>
               <?php 
                // Check if the user is logged in and display the appropriate icons
                if (isset($_SESSION['user_id'])): ?>
                    <a href="userprofile.php" class="icons-btn d-inline-block"><span class="bx bx-user"></span></a>
                    <a href="logout.php" class="icons-btn d-inline-block"><span class="bx bx-log-out"></span></a>
                <?php else: ?>
                    <a href="LoginPage.php" class="icons-btn d-inline-block"><span class="bx bx-user"></span></a>
                <?php endif; ?>
                <a href="ShoppingCart.php" class="icons-btn d-inline-block bag">
                    <span class="bx bx-cart"></span>
                    
                </a>
                <a href="#" class="site-menu-toggle js-menu-toggle ml-3 d-inline-block d-lg-none"><span class="icon-menu"></span></a>
            </div>
        </div>
    </div>
</div>



<!-- Mobile Menu (hidden by default) -->
<div class="mobile-menu d-lg-none" style="display: none;">
  <ul class="site-menu">
    <li><a href="home.php">HOME</a></li>
    <li class="has-children">
      <a href="all-products.php">SHOP</a>
      <ul class="dropdown">
        <li class="has-children">
          <a href="ProductEyes.php">EYES</a>
          <ul class="dropdown">
            <li><a href="ProductEyes.php">EYESHADOW</a></li>
          </ul>
        </li>
        <li class="has-children">
          <a href="ProductBlusher.php">CHEEKS</a>
          <ul class="dropdown">
            <li><a href="ProductBlusher.php">BLUSH</a></li>
          </ul>
        </li>
        <li class="has-children">
          <a href="Lips.php">LIPS</a>
          <ul class="dropdown">
            <li><a href="LipPlummer.php">LIP PLUMMER</a></li>
            <li><a href="LipMatte.php">LIP MATTE</a></li>
            <li><a href="LipCream.php">LIP CREAM</a></li>
            <li><a href="LipTint.php">LIP TINT</a></li>
          </ul>
        </li>
      </ul>
    </li>
    <li><a href="aboutus2.php">ABOUT US</a></li>
    <li><a href="FindMyShade1.php">FIND MY SHADE</a></li>
  </ul>
</div> 



<!--  jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Toggling Mobile Menu -->
<script>
   $(document).ready(function() {
    $('.js-menu-toggle').click(function(e) {
        e.preventDefault();
        $('.mobile-menu').slideToggle(); 
    });
});

</script>
   <div id="searchContainer" style="display: none; justify-content: center; align-items: center; max-width: 1100px; margin: 0 auto; height: 10vh;">
    <form action="search_results.php" method="GET" style="display: flex; align-items: center; width: 100%; max-width: 600px;">
        <input type="text" name="query" id="searchInput" class="form-control" style="padding: 10px; flex: 1;" placeholder="Search Products" />
        <button type="submit" class="btn" id="searchButton" style="background-color: #ff69b4; color: white; border: none; margin-left: 10px;">Search</button>
    </form>
</div>


<script>
    const searchContainer = document.getElementById('searchContainer');
    const toggleButton = document.getElementById('toggleSearch');

    toggleButton.addEventListener('click', () => {
        if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
            searchContainer.style.display = 'flex';
            toggleButton.textContent = 'Hide Search';
        } else {
            searchContainer.style.display = 'none';
            toggleButton.textContent = 'Show Search';
        }
    });
</script>




  <!-- Scrip for Dropdown and search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.mobile-menu .has-children > a');
    
    menuItems.forEach(menuItem => {
        menuItem.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default anchor click behavior
            
            // Find the corresponding dropdown menu
            const dropdown = menuItem.nextElementSibling;
            
            // Toggle the visibility of the dropdown
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        });
    });
});


document.getElementById('searchToggle').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default anchor click behavior
    const searchContainer = document.getElementById('searchContainer');
    // Toggle display of the search container
    if (searchContainer.style.display === 'none') {
        searchContainer.style.display = 'flex'; // Show centered search input		        
		searchInput.style.width = '70%' ; // Smaller size
        document.getElementById('searchInput').focus(); 
    } else {
        searchContainer.style.display = 'none'; // Hide search input
    }
});

</script>



<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="js/jquery-3.4.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap-4.4.1.js"></script>
