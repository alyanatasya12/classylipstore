<?php
include 'mysqli.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $user_message = trim($_POST['user_message']);


    if (!empty($user_name) && !empty($user_email) && !empty($user_message)) {
        $stmt = $conn->prepare("INSERT INTO user_inquiries (user_name, user_email, user_message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_name, $user_email, $user_message);

         
        if ($stmt->execute()) {
            // Success message 
            echo "<script>
                alert('Thank you for your inquiry! We will get back to you within 24 hours.');
                window.location.href = 'ContactUs.php';
            </script>";
        } else {
            // Error message
            echo "<script>
                alert('There was an error submitting your inquiry. Please try again later.');
                window.location.href = 'ContactUs.php'; 
            </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
            alert('All fields are required. Please fill in all the fields.');
            window.history.back(); 
        </script>";
    }

   
    $conn->close();
}

 
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >Contact Us</title>
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
    <link href="contactus.css" rel="stylesheet" type="text/css">
</head>
<body>
  <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   
	<br><br>
        <div class="container3">
            <section class="questions">
                <h2>Any Questions?</h2>
				
                <p>Do not hesitate to ask any questions that come to your mind!</p>
               <p class="email us">Email us at <a href="mailto:Classylip@gmail.com">classylip@gmail.com</a></p>
            </section>
<br><br>
            <section class="connect">
                <h2>Contact Us</h2>
                <p>Feel free to contact us below with your inquiries and we will strive to get back to you within 24 hours.</p>
                <p class="note">Be aware of phishing scams. Please note that outreach from our team will be conducted through our official social accounts (Instagram: @classylip, TikTok: @classylip) and email domains (community@classylip.com) only.</p>
            </section>
        </div>
   <br>
	    <div class="contact-form">
        <h1 style="font-family: 'Sorts Mill Goudy', serif;">Contact Details</h1>
        <p class="subtext">Reach us below with your inquiries and we'll try our best to respond within the next 24-48 hours</p>
        <form action="contactus.php" method="POST">
            <div class="form-group">
                <label for="user_name">Name</label>
                <input type="text" id="user_name" name="user_name" required>
                
                <label for="user_email" class="right-label">Email</label>
                <input type="email" id="user_email" name="user_email" required>
            </div>
            <div class="form-group full-width">
                <label for="user_message">Message</label>
                <textarea id="user_message" name="user_message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btnsubmit">SEND</button>
        </form>
    </div>
	  
	  
	  <br><br>
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