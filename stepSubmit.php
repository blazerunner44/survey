<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
require('mysql.php');

if($_GET['step'] == 1){
	$name = $_POST['firstName'] . " ". $_POST['lastName'];
	$rand = substr(md5(rand()), 0, 10);

	mysqli_query($con, "INSERT INTO takers (ip,name,houseNumber,houseStreet,houseCity,houseZip,session_token) VALUES ('$_SERVER[REMOTE_ADDR]','$name','$_POST[houseNumber]','$_POST[houseStreet]','$_POST[houseCity]','$_POST[houseZip])','$rand')");

	echo "<script>var session_token='{$rand}';</script>";
}elseif($_GET['step'] == 2){
	print_r($_POST);
	foreach($_POST as $key=>$response){
		echo $key;
		echo $response;
		mysqli_query($con, "INSERT INTO results (question_id,response,session_token) VALUES ('$key','$response','$_GET[session_token]')");
	}
	mysqli_query($con, "UPDATE takers SET complete=1 WHERE session_token='$_GET[session_token]'");
}
mysqli_close($con);
?>