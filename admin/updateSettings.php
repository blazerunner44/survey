<?php
require_once('Question.php');
require('../mysql.php');

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1); 

//Prepared statement for DB
$stmt = mysqli_prepare($con, "UPDATE settings SET value=? WHERE name=?");

	foreach($_POST as $setting=>$value){
		echo $setting;
		echo $value;
		$bind = mysqli_stmt_bind_param($stmt, "ss", $value, $setting);
        mysqli_stmt_execute($stmt);
	}
	mysqli_error($con);
mysqli_stmt_close($stmt);

header('Location: results.php');
?>
