<?php
// Define DB variables here
$host = "localhost";
$username = "root";
$password = "";
$database = "survey";
//End of DB variables
$con=mysqli_connect($host,$username,$password,$database) or die(mysqli_error($con));
?>