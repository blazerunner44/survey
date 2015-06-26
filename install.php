<?php
if($_POST['submit']){
	$myfile = fopen("mysql.php", "w") or die("Unable to open file!");
	$txt = "<?php \$con = mysqli_connect('{$_POST[host]}','{$_POST[user]}','{$_POST[password]}','{$_POST[dbName]}'); ?>";
	fwrite($myfile, $txt);

	require('mysql.php');

	//Create QUESTIONS table
	$query="CREATE TABLE questions (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	question VARCHAR(500) NOT NULL,
	type VARCHAR(500) NOT NULL,
	description VARCHAR(500),
	choices VARCHAR(2000),
	pos INT(10) NOT NULL
	)";
	mysqli_query($con, $query);

	//Create RESULTS table
	$query="CREATE TABLE results (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	question_id INT(10) NOT NULL,
	response VARCHAR(5000) NOT NULL,
	session_token VARCHAR(10)
	)";
	mysqli_query($con, $query);

	//Create TAKERS table
	$query="CREATE TABLE settings (
	name VARCHAR(100) PRIMARY KEY NOT NULL,
	value VARCHAR(100) NOT NULL
	)";
	mysqli_query($con, $query);

	//Populate settings table
	mysqli_query($con, "INSERT INTO settings (name,value) VALUES ('name', '$_POST[name]')");
	mysqli_query($con, "INSERT INTO settings (name,value) VALUES ('email', '$_POST[email]')");

	header('Location: results.php');
}
?>
<html>

<head>
	<title>Install Survey</title>
	<link href="styles/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="container">
	<h1>Install PHP Survey</h1>
	<form method="post" action="install.php">
		<div class="row">
			<div class="form-group col-sm-6">
			    <label for="name">Name of Survey</label>
			    <input type="text" class="form-control" id="name" name="name" placeholder="Name of your survey">
			</div>
			<div class="form-group col-sm-6">
			    <label for="email">Your Email</label>
			    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
				    <label for="host">Database Host</label>
				    <input type="text" class="form-control" id="host" name="host" value="localhost" placeholder="Enter the database host address">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
				    <label for="dbName">Database Name</label>
				    <input type="text" class="form-control" id="dbName" name="dbName" placeholder="Enter the name of your database">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
				    <label for="user">Database User</label>
				    <input type="text" class="form-control" id="user" name="user" placeholder="Enter database user">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
				    <label for="password">Database Password</label>
				    <input type="password" class="form-control" id="password" name="password" placeholder="Enter the password of your database">
				</div>
			</div>
		</div>
		
		<input type="submit" class="btn btn-default" name="submit"/>
	</div>
	</form>