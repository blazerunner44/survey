<?php
session_start();
require_once('../Survey.php');
if($_SESSION['auth'] == True){
	header('Location: results.php');
}

if(isset($_POST['submit'])){
	if(Survey::verifyUser($_POST['username'], $_POST['password'])){
		$_SESSION['auth'] = True;
		header('Location: results.php');
	}else{
		$fail = True;
	}
}

$survey = new Survey();
?>
<html>
	<head>
		<script src="../includes/jquery.min.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<title>Log In</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="http://bootswatch.com/yeti/bootstrap.min.css">
		<style type="text/css">
		body{
			background-color: rgb(254,254,254);
		}
		#container {
			margin:auto;
			height: 300px;
			width:450px;
			top:0;
			bottom: 0;
			left: 0;
			right: 0;
			position: absolute;
			background-color: rgb(245,245,245);
			/*border: 1px solid gray;
			border-radius: 2px;*/
			padding: 10px;
			/*box-shadow: 1px 1px 1px -1px black;*/
		}
		input{
			/*width:100%;
			margin:auto;
			height:40px;
			font-size:18px;*/
			margin-bottom: 25px;
		}
		button{
			float:right;
		}
		.bg-danger{
			margin-bottom: 5px;
		}
		</style>
	</head>

	<body>
		<div id="container">
			<h1><?php echo $survey->name; ?></h1>
			<?php
			if($fail){
				echo "<div class='bg-danger'>Incorrect! Please try again.</div>";
			}
			?>
			<form method="post">
				<input type="text" class="form-control" placeholder="Username" name="username" required />
				<input type="password" class="form-control" placeholder="Password" name="password" required />
				<button type="submit" name="submit" class="btn btn-primary">Log In</button>
			</form>
		</div>