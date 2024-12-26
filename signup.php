<?php session_start();
include_once('includes/mysqli.php');
include ('RegisterPage.php');
error_reporting(0);
if(isset($_POST['submit']))
{
$first_name=$_POST['firstName'];
$last_name=$_POST['lastName'];
$email=$_POST['email'];
$password=md5($_POST['inputuserpwd']);
$sql=mysqli_query($con,"select user_id from users where email='$email'");
$count=mysqli_num_rows($sql);
if($count==0){
$query=mysqli_query($con,"insert into users(firstName,lastName, email,password) values('$first_name','$last_name','$email','$password')");
if($query)
{
    echo "<script>alert('You are successfully register');</script>";
    echo "<script type='text/javascript'> document.location ='LoginPage.php'; </script>";
}
else{
echo "<script>alert('Not register something went worng');</script>";
    echo "<script type='text/javascript'> document.location ='signup.php'; </script>";
} } else{
 echo "<script>alert('Email id already registered with another accout. Please try  with another email id.');</script>";
    echo "<script type='text/javascript'> document.location ='signup.php'; </script>";   
}}
?>