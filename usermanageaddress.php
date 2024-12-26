<?php 
session_start(); 
require 'mysqli.php'; 

// Check if the user is logged in
if (strlen($_SESSION['user_id']) == 0) {
    header('location:logout.php');
    exit; 
}

// Fetch existing address data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM useraddresses WHERE user_id = ? AND is_default = 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);
$address_id = null;
$address = '';
$city = '';
$state = '';
$postcode = '';
$phone_number = '';

// Populate the form with existing data 
if ($user_data) {
    $address_id = $user_data['address_id'];
    $address = $user_data['address'];
   
    $city = $user_data['city'];
    $state = $user_data['state'];
    $postcode = $user_data['postcode'];
    $phone_number = $user_data['phone_number'];
}

// Handle the form submission for adding or updating the address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $postcode = htmlspecialchars($_POST['postcode']);
    $phone_number = htmlspecialchars($_POST['phone_number']);

    if ($address_id) {
        // Update existing address
        $update_query = "UPDATE useraddresses SET 
            address = ?,  city = ?, state = ?, postcode = ?, phone_number = ? 
            WHERE address_id = ?";
        
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "sssssi", $address, $city, $state, $postcode, $phone_number, $address_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            echo "<script>alert('Address updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating address. Please try again.');</script>";
        }
    } else {
        
    // Insert new address
$insert_query = "INSERT INTO useraddresses (user_id, address, city, state, postcode, phone_number, is_default) 
                 VALUES (?, ?, ?, ?, ?, ?, 1)";

$insert_stmt = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "isssss", $user_id, $address,$city, $state, $postcode, $phone_number);

        if (mysqli_stmt_execute($insert_stmt)) {
            echo "<script>alert('Address added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding address. Please try again.');</script>";
        }
    }

    // Redirect to manage addresses page
    echo "<script type='text/javascript'> document.location ='usermanageaddress.php'; </script>";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

	 <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title >My Account | Billing Address</title>
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
   <style>
	  

@media (max-width: 768px) {
  /* Mobile-specific styles */
  .container {
    width: 100%;
    padding: 10px;
  }
}

.main-container {
  width: 100%;
  max-width: 100%;
  padding: 0 10px; /* Optional, to add some spacing */
}

html, body {
  overflow-x: hidden;
}
/* Base */
body {
  line-height: 1.7;
  color: #000000;
  font-weight: 300;
  font-size: 16px; 
	font-family:Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"
 }

::-moz-selection {
  background: #000;
  color: #fff; }

::selection {
  background: #000;
  color: #fff; }

a {
  -webkit-transition: .3s all ease;
  -o-transition: .3s all ease;
  transition: .3s all ease; }
  a:hover {
    text-decoration: none; }

.text-black {
  color: #000; }

.site-wrap:before {
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
  background: rgba(0, 0, 0, 0.6);
  content: "";
  position: absolute;
  z-index: 2000;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  opacity: 0;
  visibility: hidden; }

.offcanvas-menu .site-wrap {
  position: absolute;
  height: 100%;
  width: 100%;
  z-index: 2;
  overflow: hidden; }
  .offcanvas-menu .site-wrap:before {
    opacity: 1;
    visibility: visible; }

.btn {
  height: 43px;
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
  text-transform: uppercase;
  font-size: 13px; }
  .btn:hover, .btn:active, .btn:focus {
    outline: none;
    -webkit-box-shadow: none;
    box-shadow: none; }
  .btn.btn-black {
    padding: 12px 30px;
    background: #000;
    color: #fff; }
    .btn.btn-black:hover, .btn.btn-black:active, .btn.btn-black:focus {
      background-color: #333333; }

.form-control {
  height: 43px; }
  .form-control:active, .form-control:focus {
    border-color: #FFFFFF; }
  .form-control:hover, .form-control:active, .form-control:focus {
    -webkit-box-shadow: none !important;
    box-shadow: none !important; }

.site-section {
  padding: 2.5em 0; }
  @media (min-width: 768px) {
    .site-section {
      padding: 5em 0; } }
  .site-section.site-section-sm {
    padding: 4em 0; }

.site-section-heading {
  font-size: 30px;
  color: #25262a;
  position: relative; }
  .site-section-heading:before {
    content: "";
    left: 0%;
    top: 0;
    position: absolute;
    width: 40px;
    height: 2px;
    background: #FFFFFF; }
  .site-section-heading.text-center:before {
    content: "";
    left: 50%;
    top: 0;
    -webkit-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    transform: translateX(-50%);
    position: absolute;
    width: 40px;
    height: 2px;
    background: #ee4266; }

.border-top {
  border-top: 1px solid #edf0f5 !important; }

.site-footer {
  padding: 4em 0; }
  .site-footer ul li {
    margin-bottom: 10px; }
    .site-footer ul li a {
      color: #5c626e; }
      .site-footer ul li a:hover {
        color: #ee4266; }
  .site-footer .footer-heading {
    font-size: 14px;
    color: #25262a;
    letter-spacing: .2em;
    text-transform: uppercase; }

/* Navbar */
.site-logo a {
  text-transform: uppercase;
  letter-spacing: .2em;
  font-size: 20px;
  padding-left: 10px;
  padding-right: 10px;
  border: 2px solid #25262a;
  color: #000 !important; }
  .site-logo a:hover {
    text-decoration: none; }

.icons-btn {
  display: inline-block;
  text-align: center; }
  .icons-btn span {
    display: block;
    height: 40px;
    width: 40px;
    line-height: 40px; }
    @media (max-width: 991.98px) {
      .icons-btn span {
        width: 24px; } }

.site-menu-toggle {
  display: block;
  text-align: center;
  font-size: 28px;
  height: 40px;
  width: 40px;
  line-height: 40px; }
  .site-menu-toggle > span {
    top: 5px;
    position: relative; }

.site-navbar {
  background: #FFD9DE;
  margin-bottom: 0px;
  z-index: 1999;
  position: relative;
 }
  .site-navbar.transparent {
    background: transparent; }
  .site-navbar .site-navbar-top {
    border-bottom: 1px solid #f3f3f4;
    padding-top: 20px;
    padding-bottom: 20px;
    margin-bottom: 0px; }
    @media (min-width: 768px) {
      .site-navbar .site-navbar-top {
        padding-top: 40px;
        padding-bottom: 40px; } }
  .site-navbar .site-search-icon a span {
    display: inline-block;
    margin-right: 10px; }
  .site-navbar .site-search-icon a:hover {
    text-decoration: none; }
  .site-navbar a {
    color: #000000; }
    .site-navbar a:hover {
      color: #00000; }
  .site-navbar .icon {
    font-size: 20px; }
  .site-navbar .site-top-icons ul, .site-navbar .site-top-icons ul li {
    padding: 0;
    margin: 0;
    list-style: none; }
  .site-navbar .site-top-icons ul li {
    display: inline-block; }
    .site-navbar .site-top-icons ul li a {
      margin-right: 10px; }
      .site-navbar .site-top-icons ul li a.site-cart {
        display: block;
        position: relative; }
        .site-navbar .site-top-icons ul li a.site-cart .count {
          position: absolute;
          top: 0;
          right: 0;
          margin-right: -15px;
          margin-top: -20px;
          font-size: 13px;
          width: 24px;
          height: 24px;
          line-height: 24px;
          border-radius: 50%;
          display: block;
          text-align: center;
          background: #ee4266;
          color: #fff;
          -webkit-transition: .2s all ease-in-out;
          -o-transition: .2s all ease-in-out;
          transition: .2s all ease-in-out; }
      .site-navbar .site-top-icons ul li a:hover .count {
        -webkit-box-shadow: 0 3px 10px -4px rgba(0, 0, 0, 0.3) !important;
        box-shadow: 0 3px 10px -4px rgba(0, 0, 0, 0.3) !important;
        margin-top: -22px; }
    .site-navbar .site-top-icons ul li:last-child a {
      padding-right: 0; }
  .site-navbar .site-navigation.border-bottom {
    border-bottom: 1px solid #f3f3f4 !important; }
  .site-navbar .site-navigation .site-menu {
    margin-left: 0;
    padding-left: 0;
    margin-bottom: 0; }
    .site-navbar .site-navigation .site-menu .active > a {
      color: #000000; }
    .site-navbar .site-navigation .site-menu a {
      text-decoration: none !important;
      font-size: 15px;
      display: inline-block; }
    .site-navbar .site-navigation .site-menu > li {
      display: inline-block;
      padding: 10px 5px; }
      .site-navbar .site-navigation .site-menu > li > a {
        padding: 10px 10px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #000000;
        font-size: 15px;
        text-decoration: none !important; }
        .site-navbar .site-navigation .site-menu > li > a:hover {
          color: #000000; }
    .site-navbar .site-navigation .site-menu .has-children {
      position: relative; }
      .site-navbar .site-navigation .site-menu .has-children > a {
        position: relative;
        padding-right: 20px; }
        .site-navbar .site-navigation .site-menu .has-children > a:before {
          position: absolute;
          content: "\e313";
          font-size: 16px;
          top: 50%;
          right: 0;
          -webkit-transform: translateY(-50%);
          -ms-transform: translateY(-50%);
          transform: translateY(-50%);
          font-family: 'icomoon'; }
      .site-navbar .site-navigation .site-menu .has-children .dropdown {
        visibility: hidden;
        opacity: 0;
        top: 100%;
        position: absolute;
        text-align: left;
       
        -webkit-box-shadow: 0 0px 4px 0px rgba(0, 0, 0, 0.05);
        box-shadow: 0 0px 4px 0px rgba(0, 0, 0, 0.05);
        border-left: 1px solid #edf0f5;
        border-right: 1px solid #edf0f5;
        border-bottom: 1px solid #edf0f5;
        padding: 0px 0;
        margin-top: 20px;
        margin-left: 0px;
        background: #fff;
        -webkit-transition: 0.2s 0s;
        -o-transition: 0.2s 0s;
        transition: 0.2s 0s; }
        .site-navbar .site-navigation .site-menu .has-children .dropdown a {
          font-size: 16px;
          text-transform: none;
          letter-spacing: normal;
          -webkit-transition: 0s all;
          -o-transition: 0s all;
          transition: 0s all; }
        .site-navbar .site-navigation .site-menu .has-children .dropdown .active > a {
          color: #ee4266 !important; }
        .site-navbar .site-navigation .site-menu .has-children .dropdown > li {
          list-style: none;
          padding: 0;
          margin: 0;
          min-width: 200px; }
          .site-navbar .site-navigation .site-menu .has-children .dropdown > li > a {
            padding: 9px 20px;
            display: block; }
            .site-navbar .site-navigation .site-menu .has-children .dropdown > li > a:hover {
              background: #f4f5f9;
              color: #25262a; }
          .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children > a:before {
            content: "\e315";
            right: 20px; }
          .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children > .dropdown, .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children > ul {
            left: 100%;
            top: 0; }
          .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children:hover > a, .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children:active > a, .site-navbar .site-navigation .site-menu .has-children .dropdown > li.has-children:focus > a {
            background: #f4f5f9;
            color: #25262a; }
      .site-navbar .site-navigation .site-menu .has-children a {
        text-transform: uppercase; }
      .site-navbar .site-navigation .site-menu .has-children:hover > a, .site-navbar .site-navigation .site-menu .has-children:focus > a, .site-navbar .site-navigation .site-menu .has-children:active > a {
        color: #000000; }
      .site-navbar .site-navigation .site-menu .has-children:hover, .site-navbar .site-navigation .site-menu .has-children:focus, .site-navbar .site-navigation .site-menu .has-children:active {
        cursor: pointer; }
        .site-navbar .site-navigation .site-menu .has-children:hover > .dropdown, .site-navbar .site-navigation .site-menu .has-children:focus > .dropdown, .site-navbar .site-navigation .site-menu .has-children:active > .dropdown {
          -webkit-transition-delay: 0s;
          -o-transition-delay: 0s;
          transition-delay: 0s;
          margin-top: 0px;
          visibility: visible;
          opacity: 1; }

.site-mobile-menu {
  width: 300px;
  position: fixed;
  right: 0;
  z-index: 2000;
  padding-top: 20px;
  background: #fff;
  height: calc(100vh);
  -webkit-transform: translateX(110%);
  -ms-transform: translateX(110%);
  transform: translateX(110%);
  -webkit-box-shadow: -10px 0 20px -10px rgba(0, 0, 0, 0.1);
  box-shadow: -10px 0 20px -10px rgba(0, 0, 0, 0.1);
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out; }
  .offcanvas-menu .site-mobile-menu {
    -webkit-transform: translateX(0%);
    -ms-transform: translateX(0%);
    transform: translateX(0%); }
  .site-mobile-menu .site-mobile-menu-header {
    width: 100%;
    float: left;
    margin-bottom: 20px;
    padding-left: 20px;
    padding-right: 20px; }
    .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-close {
      float: right;
      margin-top: 8px; }
      .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-close span {
        font-size: 40px;
        display: inline-block;
        padding-left: 10px;
        padding-right: 10px;
        line-height: 1;
        cursor: pointer;
        -webkit-transition: .3s all ease;
        -o-transition: .3s all ease;
        transition: .3s all ease; }
        .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-close span:hover {
          color: #25262a; }
    .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-logo {
      float: left;
      margin-top: 10px;
      margin-left: 20px; }
      .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-logo a {
        display: inline-block;
        text-transform: uppercase;
        color: #25262a;
        letter-spacing: .2em;
        font-size: 20px;
        padding-left: 10px;
        padding-right: 10px;
        border: 2px solid #25262a; }
        .site-mobile-menu .site-mobile-menu-header .site-mobile-menu-logo a:hover {
          text-decoration: none; }
  .site-mobile-menu .site-mobile-menu-body {
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;
    position: relative;
    padding: 20px;
    height: calc(100vh - 52px);
    padding-bottom: 150px; }
  .site-mobile-menu .site-nav-wrap {
    padding: 0;
    margin: 0;
    list-style: none;
    position: relative; }
    .site-mobile-menu .site-nav-wrap a {
      padding: 10px 20px;
      display: block;
      position: relative;
      color: #212529; }
      .site-mobile-menu .site-nav-wrap a:hover {
        color: #000000; }
    .site-mobile-menu .site-nav-wrap li {
      position: relative;
      display: block; }
      .site-mobile-menu .site-nav-wrap li.active > a {
        color: #ee4266; }
    .site-mobile-menu .site-nav-wrap .arrow-collapse {
      position: absolute;
      right: 0px;
      top: 10px;
      z-index: 20;
      width: 36px;
      height: 36px;
      text-align: center;
      cursor: pointer;
      border-radius: 50%; }
      .site-mobile-menu .site-nav-wrap .arrow-collapse:hover {
        background: #f8f9fa; }
      .site-mobile-menu .site-nav-wrap .arrow-collapse:before {
        font-size: 18px;
        z-index: 20;
        font-family: "icomoon";
        content: "\e313";
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%) rotate(-180deg);
        -ms-transform: translate(-50%, -50%) rotate(-180deg);
        transform: translate(-50%, -50%) rotate(-180deg);
        -webkit-transition: .3s all ease;
        -o-transition: .3s all ease;
        transition: .3s all ease; }
      .site-mobile-menu .site-nav-wrap .arrow-collapse.collapsed:before {
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%); }
    .site-mobile-menu .site-nav-wrap > li {
      display: block;
      position: relative;
      float: left;
      width: 100%; }
      .site-mobile-menu .site-nav-wrap > li > a {
        padding-left: 20px;
        font-size: 20px; }
      .site-mobile-menu .site-nav-wrap > li > ul {
        padding: 0;
        margin: 0;
        list-style: none; }
        .site-mobile-menu .site-nav-wrap > li > ul > li {
          display: block; }
          .site-mobile-menu .site-nav-wrap > li > ul > li > a {
            padding-left: 40px;
            font-size: 16px; }
          .site-mobile-menu .site-nav-wrap > li > ul > li > ul {
            padding: 0;
            margin: 0; }
            .site-mobile-menu .site-nav-wrap > li > ul > li > ul > li {
              display: block; }
              .site-mobile-menu .site-nav-wrap > li > ul > li > ul > li > a {
                font-size: 16px;
                padding-left: 60px; }



.bag {
  position: relative; }
  .bag .number {
    position: absolute;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    line-height: 20px;
    color: #fff;
    font-size: 12px;
    background: #ee4266;
    right: -10px; }

.search-wrap {
  position: absolute;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #fff;
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  -webkit-transition: .5s all ease;
  -o-transition: .5s all ease;
  transition: .5s all ease; }
  .search-wrap .container {
    position: relative;
    height: 100%; }
  .search-wrap.active {
    opacity: 1;
    visibility: visible; }
  .search-wrap .form-control {
    position: absolute;
    top: 50%;
    width: 100%;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    border: none;
    z-index: 3;
    font-size: 40px; }
    @media (max-width: 991.98px) {
      .search-wrap .form-control {
        font-size: 20px; } }
  .search-wrap .search-close {
    z-index: 4;
    position: absolute;
    right: 20px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%); }
    .search-wrap .search-close span {
      font-size: 30px; }


.bx-search {
    font-size: 24px; 
}

.bx-user {
    font-size: 24px; 
}

.bx-cart {
    font-size: 24px; 
}

.bx-log-out {
	 font-size: 24px;
}
	   
/* General Layout */
.container4 {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.col-md-8, .col-md-4 {
  
  padding: 20px;

 
}

/* Profile Section Width */
.col-md-8 {
  flex: 1;
  min-width: 60%;
}

/* Sidebar Width */
.col-md-4 {
  width: 300px;
}

@media (max-width: 768px) {
  .col-md-8, .col-md-4 {
    width: 100%;
    min-width: unset;
  }
}

/* Profile Panel */
.panel-heading {
  background-color: #f8d7da;
  padding: 10px;
  display: flex;
  align-items: center;
}

.panel-heading h4 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin: 0;
}

.panel-heading span {
  background-color: #ff5252;
  color: #fff;
  font-size: 16px;
  width: 30px;
  height: 30px;
  line-height: 30px;
  border-radius: 50%;
  display: inline-block;
  text-align: center;
  margin-right: 10px;
}

.panel-body {
  padding: 20px;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 5px 5px;
}

/* Form Styles */
h4 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  font-size: 14px;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 5px;
}

.form-control {
  width: 100%;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.form-control:focus {
  border-color: #ff5252;
  box-shadow: none;
}

/* Update Button */
.btn-primary {
  background-color: #aaa;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  font-size: 14px;
  font-weight: bold;
}

.btn-primary:hover {
  background-color: #888;
}

/* Sidebar Styles */
.checkout-progress-sidebar .panel-heading {
  background-color: #f8f9fa;
  padding: 10px;
  font-size: 16px;
  font-weight: bold;
  color: #333;
  border-radius: 5px 5px 0 0;
}

.checkout-progress-sidebar .panel-body {
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 0 0 5px 5px;
}

.nav-checkout-progress {
  padding-left: 0;
  margin: 0;
  list-style-type: none;
}

.nav-checkout-progress li {
  margin: 10px 0;
}

.nav-checkout-progress li a {
  color: #333;
  font-size: 14px;
  text-decoration: none;
  display: block;
}

.nav-checkout-progress li a:hover {
  color: #ff5252;
  text-decoration: underline;
}
/* Footer */
footer {
    background-color: #FFD9DE;
    color: black;
    padding: 50px 0;
    text-align: center;
    border: none; /* Ensure no border on footer */
}

.footer .col-footer h5 {
    font-weight: bold;
    margin-bottom: 15px;
}

.footer ul {
    list-style: none;
    padding: 0;
    margin: 0; /* Reset margin to prevent any outer space */
    border: none; /* Remove any border on list */
}

.footer ul li a {
    color: #000000;
    text-decoration: none;
}

.footer ul li a:hover {
    color: #cccccc;
}


	  </style>
	  
</head>
  <body>
 
<!-- Include Bootstrap JS (optional, for toggle functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

     <!-- Navigation Bar -->
<?php
	include('includes/navigation.php');
?>   

<div class="container4">
  <!-- Left Section (Profile Information) -->
  <div class="col-md-8 ">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span>2</span>
        <h4 class="unicase-checkout-title">BILLING ADDRESS</h4>
      </div>
      <div class="panel-body">
        <h4>Billing Details</h4>
       <form class="register-form" role="form" method="post" action="usermanageaddress.php">
   
  
    <div class="form-group">
        <label for="address">Address<span>*</span></label>
        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="city">City<span>*</span></label>
        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="state">State<span>*</span></label>
        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user_data['state'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="postcode">Postcode<span>*</span></label>
        <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo htmlspecialchars($user_data['postcode'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="phone_number">Contact No. <span>*</span></label>
        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>" maxlength="10"required>
    </div>
    <button type="submit" name="update" class="btn btn-primary" style= "  background-color: #ff69b4;
 ">UPDATE</button>
</form>

      </div>
    </div>
  </div>

  <!-- Right Section (Checkout Progress Sidebar) -->
  <div class="col-md-4">
    <div class="checkout-progress-sidebar">
  <div class="panel panel-default">
    <div class="panel-heading">YOUR ACCOUNT</div>
    <div class="panel-body">
      <ul class="nav-checkout-progress list-unstyled">
        <li><a href="userprofile.php">My Account</a></li>
        <li><a href="usermanageaddress.php"> Billing Address</a></li>
        <li><a href="orderhistory.php">Order History</a></li>
      </ul>
    </div>
  </div>
</div>

  </div>
</div>

  <!-- Footer  -->
<?php
	include('includes/footer.php');
?>  
<script>
document.querySelectorAll('.panel-heading').forEach(heading => {
  heading.addEventListener('click', function() {
    const panelBody = this.nextElementSibling;
    panelBody.style.display = panelBody.style.display === 'none' ? 'block' : 'none';
  });
});


</script>

 
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
	  <script src="js/jquery-3.4.1.min.js"></script>
	  

  </body>
</html>