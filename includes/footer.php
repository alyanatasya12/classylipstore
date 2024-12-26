



<!DOCTYPE html>
	 <style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
}

/* Footer styles */
footer {
    width: 100vw; 
    background-color: #FFD9DE; 
    color: black;
    padding: 20px 0; 
    position: relative; 
}

/* Footer heading */
.footer-heading {
    font-size: 14px;
    background-color: #FFD9DE; 
    color: #25262a;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    margin-bottom: 15px;
    font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif !important;
}

/* Footer links */
.footer-link {
    color: #5c626e;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-link:hover {
    color: #ee4266;
}

/* Payment icons */
.payment-icons img {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
    pointer-events: none; 
}

.payment-icons img:last-child {
    margin-right: 0;
}

    </style>
</head>

<!--FOOTER -->   
<footer class="text-center text-black py-4">
    <div class="container">
        <div class="row align-items-start">
            <!-- Logo Section -->
            <div class="col-lg-3 col-md-6 mb-4">
                <img src="images/logonew.png" alt="Classylip Logo" width="120" height="120" class="classylip mb-2" />
            </div>
			
            <!-- Customer Care Section -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading"><b>CUSTOMER CARE</b></h5>
                <ul class="list-unstyled">
                    <li class="payment-icons">
                        <img src="images/card.png" alt="Mastercard" width="40" height="40" class="mx-2">
                        <img src="images/visa.png" alt="Visa" width="40" height="40" class="mx-2">
                    </li>
                    <li><a href="TermsAndConditions.php" class="footer-link">Terms & Conditions</a></li>
                </ul>
            </div>
			
            <!-- Information Section -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading"><b>INFORMATION</b></h5>
                <ul class="list-unstyled">
                    <li><a href="aboutus2.php" class="footer-link">About Us</a></li>
                    <li><a href="ContactUs.php" class="footer-link">Contact Us</a></li>
                    <li><a href="faq.php" class="footer-link">FAQ</a></li>
                </ul>
            </div>
			
            <!-- Follow Us Section -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading"><b>FOLLOW US</b></h5>
                <ul class="list-unstyled">
                    <li><a href="https://www.instagram.com/clssylip?igsh=bHR6M2Zzc2Flb242" class="footer-link">Instagram</a></li>
                    <li><a href="https://www.tiktok.com/@clssylip?_t=8mtgaqzRzlp&_r=1" class="footer-link">Tiktok</a></li>
                    <li><a href="https://my.shp.ee/eLb5vnS" class="footer-link">Shopee</a></li>
                </ul>
            </div>  
        </div>
    </div>
	
    <div class="container text-center">
        <div class="row">
            <div class="col-12">
                <p class="mb-0">Copyright © ClassyLip. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="js/jquery-3.4.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap-4.4.1.js"></script>
