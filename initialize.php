<?php
require('mysql.php');

//Create QUESTIONS table
$query="CREATE TABLE questions (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
question VARCHAR(500) NOT NULL,
type VARCHAR(500) NOT NULL,
description VARCHAR(500),
choices VARCHAR(2000)
)";
mysqli_query($con, $query) or die(mysqli_error($con));

//Create RESULTS table
$query="CREATE TABLE results (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
question_id INT(10) NOT NULL,
response VARCHAR(5000) NOT NULL,
session_token VARCHAR(10)
)";
mysqli_query($con, $query) or die(mysqli_error($con));

//Create TAKERS table
$query="CREATE TABLE takers (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
ip VARCHAR(20) NOT NULL,
session_token VARCHAR(10) NOT NULL,
name VARCHAR(100),
complete TINYINT(1)
)";
mysqli_query($con, $query) or die(mysqli_error($con));

?>