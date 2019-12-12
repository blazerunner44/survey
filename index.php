<?php
require 'class/Question.php';
require 'class/Survey.php';

require_once('mysql.php');
$survey = new Survey();
$questions = $survey->getQuestions();
// var_dump($questions);
// exit;
?>

<!DOCTYPE html>
<html>
<head>

<title><?php echo $survey->name; ?></title>
<meta name="description" content="We would love to hear your feedback!">

<!-- Needed for slider -->
<script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
<script src="bootstrap/slider/jquery-ui.min.js"></script>
<link href="bootstrap/slider/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script src="bootstrap/slider/jquery-ui-slider-pips.js"></script>
<link href="bootstrap/slider/jquery-ui-slider-pips.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

<style type="text/css">
label {
	margin-top: 10px;
}
.container p {
	margin-top: 20px;
}
</style>
</head>

<body>
<?php include('nav_body.php'); ?>


<div class="container">
	<div class="row">
      <h1><?php echo $survey->name; ?></h1>
	  <p><?php echo $survey->description; ?></p>
	  <hr>
    	<?php
		if (reset($_POST)) { //If first element in array is true
			require('mysql.php');
			//Insert Feedback to database
      // foreach($_POST as $var => $val)
      // {
      //    $_POST[$var] = mysqli_real_escape_string($con, $val);
      // }
      $_POST = Survey::escapeInput($_POST);

			$rand = substr(md5(rand()), 0, 10);

      //Prepared statement for DB
      $stmt = mysqli_prepare($con, "INSERT INTO results (question_id,response,session_token) VALUES (?,?,'$rand')");
			foreach($_POST as $key=>$response){
				$bind = mysqli_stmt_bind_param($stmt, "is", $key, $response);
        mysqli_stmt_execute($stmt);
			}
      mysqli_stmt_close($stmt);

			// //Send email with feedback to admin
			$emailMessage="<h3>Survey Submitted</h3>";
      $result = mysqli_query($con, "SELECT * FROM questions ORDER BY pos ASC");
      while($row = mysqli_fetch_array($result)){
        $num = $row['id'];
        $emailMessage.="<b>{$row[question]}</b><p>" .stripslashes($_POST[$num]) ."</p>";
      }
			$emailMessage.="<hr>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: no-reply <{$survey->email}>";
			mail($survey->email, $survey->name.' Submission', $emailMessage, $headers);
			// //Log the feedback received

			mysqli_close($con);
			
			echo '<div class="success bg-success" style="height:50px; padding-top:10px; padding-left:30px; font-size:16px">Thank you for your feedback. It is greatly appriciated!</div>';
		  
    }
		?>
    </div>
    
    <form method="post" action="">
      <?php
      foreach($questions as $question){
        echo $question->getHTML();
      }
      ?>
    <button type="submit" id="submit" class='btn btn-success'>Submit</button>
    </form>
</div>
</body>
</html>