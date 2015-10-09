<?php

// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1); 
?>
<!doctype html>
<html>
<head>
<?php
require('Survey.php');
require_once('mysql.php');
$survey = new Survey();
?>

<title><?php echo $survey->name; ?></title>
<meta name="description" content="We would love to hear your feedback!">

<!-- Needed for slider -->
<script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
<script src="bootstrap/slider/jquery-ui.min.js"></script>
<link href="bootstrap/slider/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script src="bootstrap/slider/jquery-ui-slider-pips.js"></script>
<link href="bootstrap/slider/jquery-ui-slider-pips.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="http://bootswatch.com/yeti/bootstrap.min.css">

<style type="text/css">
/*#submit {
	border: 0;
	background-color: rgba(237,89,35,1.00);
	color: #FFFFFF;
	font-weight: bold;
	width: 100px;
	height: 40px;
	margin-bottom: 20px;
}*/
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
              //Connect to DB and get questions
              require('mysql.php');

              $result = mysqli_query($con, "SELECT * FROM questions ORDER BY pos ASC");
              while($row = mysqli_fetch_array($result)){
                $choices = json_decode($row['choices']);
                //print_r($choices);
                switch($row['type']){
                  case 'text':
                    echo "<div class='form-group col-sm-12'>

                              <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                              <input type='text' class='form-control' name='{$row[id]}' required placeholder='Enter response' />

                          </div>";
                          break;
                  case 'paragraph':
                    echo "<div class='form-group col-sm-12'>

                              <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                              <textarea class='form-control' rows='5' maxlength='5000' name='{$row[id]}' required></textarea>

                          </div>";
                          break;
                  case 'yn':
                    echo "<div class='col-sm-12'>

                        <h4>{$row[question]}<small> {$row[description]}</small></h4><br>

                        <div class='btn-group' data-toggle='buttons'>

                          <label class='btn btn-primary btn-lg'>

                            <input type='radio' name='{$row[id]}' id='option2' autocomplete='off' value='Yes'> Yes

                          </label>

                          <label class='btn btn-primary btn-lg'>

                            <input type='radio' name='{$row[id]}' id='option3' autocomplete='off' value='No'> No

                          </label>

                        </div>

                     </div>";
                     break;
                  case 'option':
                  case 'expanded_option':
                    echo "<div class='form-group col-sm-12' style='margin-top:13px'>

                         <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                        <select name='{$row[id]}' "; if($row['type']=='expanded_option'){echo "multiple";} echo" class='form-control'>";

                          foreach($choices as $choice){
                            echo "<option>{$choice}</option>";
                          }

                        echo "</select>

                    </div>";
                    break;
                  case 'response':
                    echo "<div class='form-group col-sm-12'>

                        <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                        <textarea rows='5' class='form-control' name='{$row[id]}'></textarea>

                    </div>";
                    break;
                  case 'slider':
                    echo "<script>
                      $(document).ready(function(){
                      $('#{$row[id]}slider')
                          .slider({
                              min: {$choices[0]},
                              max: {$choices[1]},
                              change: function(event, ui) {
                                $('#{$row[id]}').attr('value', ui.value);
                              }
                          })
                          .slider('pips', {
                              rest: 'label'
                          })
                      });
                      </script>";
                    echo "<div class='form-group col-sm-12'><label><h4>{$row[question]} <small>{$row[description]}</small></h4></label><div id='{$row[id]}slider'></div></div>";
                    echo "<input type='hidden' name='{$row[id]}' id='{$row[id]}'/>";
                    break;
                  default:
                    echo "<h5>unknown question type</h5>";
                }
              }
            ?>
	
    <button type="submit" id="submit" class='btn btn-success'>Submit</button>
    </form>
</div>
</body>
</html>