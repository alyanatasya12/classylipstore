<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >FAQ</title>
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
    <link href="faqq.css" rel="stylesheet" type="text/css">
</head>
  <body>
    <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

	  <!--faq section start-->
	  	<br>
	  <br>
	<div class="content-container">
    <h3 class="caption-with-letter-spacing hom">Shipping &amp; Delivery</h3>
<button class="accordion">What are the shipping costs?</button>
    <div class="panel">
<p class="p3">
    Standard shipping rates within Peninsular Malaysia are RM8, while shipping to Sabah and Sarawak is RM15. 
    We strive to deliver your items promptly and ensure they are packaged with care to arrive in excellent condition. 
    Please note that shipping times may vary depending on location and external factors such as public holidays or unexpected delays.
</p>
    </div>

<button class="accordion">What is the estimated delivery time for my order?</button>
    <div class="panel">
<p class="p3">
    Orders are shipped out every Tuesday and Friday to ensure timely processing and delivery. While most orders typically arrive the following day, we kindly ask that you allow up to 5 working days for delivery, depending on your location and courier service efficiency. <br> 
    Please note that during peak periods, such as promotions, holidays, or special offers, the processing and delivery times may be slightly extended due to higher order volumes. We appreciate your understanding and patience as we work to ensure your order reaches you in excellent condition.
</p>
    </div>

<button class="accordion">Do you provide international shipping services?</button>
    <div class="panel">
<p class="p3">
    Yes, we do offer international shipping to selected countries. Please be aware that delivery times may vary significantly depending on the destination and customs processing times. While we strive to ensure timely delivery, international shipments generally take longer to arrive compared to domestic orders. We appreciate your patience and understanding, and we will do our best to keep you updated throughout the shipping process.
</p>
    </div>

    <br><br>

    <h3 class="caption-with-letter-spacing hom">Products and Orders</h3>
<button class="accordion">Is it possible to modify my order after it has been placed?</button>
    <div class="panel">
<p class="p3" >
    We regret to inform you that all sales are considered final. Once an order has been placed and processed, we are unable to accommodate modifications, cancellations, or returns. We appreciate your understanding and encourage you to review your order carefully before completing your purchase.
</p>
    </div>

<button class="accordion">Am i able to edit my shipping address after placing an order?</button>
    <div class="panel">
<p class="p3">
    We regret to inform you that once an order is placed, the shipping address provided at the time of purchase will be used for delivery. We kindly ask that you double-check your address before completing your order to avoid any issues. However, if there has been an error, please contact us via email as soon as possible, and we will do our best to assist you in resolving the matter.
</p>
    </div>

<button class="accordion">Where are your products manufactured, and are they safe to use?</button>
    <div class="panel">
<p class="p3">
    Our products are carefully crafted using ingredients that are approved by the Ministry of Health (MOH). These ingredients include beneficial elements such as Vitamin E and Olive Oil, which are known for their nourishing properties. We ensure that all formulations meet the highest standards of safety and quality.
</p>
    </div>

    <br><br>

    <h3 class="caption-with-letter-spacing hom">Returns</h3>
<button class="accordion">What should I do if I receive the wrong items?</button>
    <div class="panel">
<p class="p3">
    If you receive a incorrect or damage item, we kindly request that you email us at <a href="mailto:classylip@gmail.com">classylip@gmail.com</a> within 7 days of receiving the product. Please include a clear photo of the damaged item, along with your order details and receipt for verification. Upon receiving your email, we will review the matter and get in touch with you within 48 hours to resolve the issue.
</p>
    </div>

<button class="accordion">What is your refund and return policy?</button>
    <div class="panel">
<p class="p3">
    Refunds will only be issued in the form of store credit points, which must be utilized within 48 hours of receipt. Should you require assistance, please contact us at <a href="mailto:classylip@gmail.com">classylip@gmail.com</a>. We will respond within 48 hours of receiving your email. 
</p>
    </div>

<button class="accordion">Am i able to return my parcel?</button>
    <div class="panel">
<p class="p3">
    Unfortunately, we are unable to process returns for products purchased through other retailers. We kindly ask that you review the return policy of the original point of purchase. Should you have any further questions or require assistance, please do not hesitate to contact us at <a href="mailto:classylip@gmail.com">classylip@gmail.com</a>.
</p>
    </div>
</div>

<br><br><br><br>
	  
	  
 <!-- Footer  -->
<?php
	include('includes/footer.php');
?>   
   
	 
	  <!---custom js linl--->
	<script>
	var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}
		</script>
	<script type="text/javascript"></script>	  
	  
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="../website/js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="../website/js/popper.min.js"></script>
    <script src="../website/js/bootstrap-4.4.1.js"></script>
</body>
</html>
