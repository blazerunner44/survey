<?php
require_once 'class/Question.php';
require_once 'class/Response.php';
require_once 'class/Survey.php';

$survey = new Survey();

if(!$survey->isInstalled()){
  header('Location: install.php');
}

$questions = Question::all();
$questions = $survey->getQuestions();

?>

<!DOCTYPE html>
<html>
<head>

<title><?php echo $survey->name; ?></title>
<meta name="description" content="We would love to hear your feedback!">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
	margin-bottom: 50px;
}
.form-check-label{
  font-size: 12pt;
  margin-top: -10px;
}
.form-check{
  margin-bottom: 20px;
}
</style>
</head>

<body>
<?php include('nav_body.php'); ?>


<div class="container">
    <h1><?= $survey->name ?></h1>
	  <p><?= $survey->description; ?></p>
    <hr>
    <?php
		if (!empty($_POST)) {
			
			$sessionToken = substr(md5(rand()), 0, 10);

			foreach($_POST as $questionId=>$responseValue){
        if(is_array($responseValue)){
          foreach ($responseValue as $checkboxResponseValue) {
            $response = new Response($questionId, $checkboxResponseValue, $sessionToken);
            $response->save();
          }
        }else{
  				$response = new Response($questionId, $responseValue, $sessionToken);
          $response->save();
        }
			}

			// //Send email with feedback to admin
			$emailMessage="<h3>Survey Submitted</h3>";
      foreach ($questions as $question) {
        if(is_array($_POST[$question->pk])){
          $responseValue = implode(' | ', $_POST[$question->pk]);
        }else{
          $responseValue = $_POST[$question->pk];
        }
        $emailMessage.="<b>{$question->title}</b><p>" . htmlentities($responseValue) ."</p>";
      }
        
			$emailMessage.="<hr>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: no-reply <{$survey->email}>";
			mail($survey->email, $survey->name.' Submission', $emailMessage, $headers);
			
			echo '</div><div class="alert alert-success" style="height:50px; padding-top:10px; padding-left:30px; font-size:16px">Thank you for your feedback. It is greatly appriciated!</div>';
		  exit;
    }
		?>
    
    <form method="post" action="">
      <?php
      if(empty($questions)){
        echo '<div class="alert alert-secondary"><strong>No questions!</strong> No questions have been configured. Please setup your survey <a href="admin/">here!</a></p></div>';
      }
      foreach($questions as $question){
        echo '<div style="margin: 50px 0px;">' . $question->getHTML() . '</div>';
      }
      ?>
      <button type="submit" id="submit" style="margin:25px 0;" class='btn btn-success'>Submit Survey</button>
    </form>
</div>
</body>
</html>