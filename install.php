<?php
if($_POST['submit']){
	$myfile = fopen("mysql.php", "w") or die("Unable to open file!");
	$info = "$host = '{$_POST[host]}'; $user='$_POST[user]'; $pass='$_POST[password]'; $dbName='$_POST[dbName]';";
	fwrite($myfile, $info);
	fclose($myfile);
}
?>
<html>

<head>
	<title>Install Survey</title>
	<link href="styles/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="container">
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
				    <input type="text" class="form-control" id="host" name="host" placeholder="Enter the database host address">
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