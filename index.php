<?php require('mysql.php');?>

<!doctype html>

<html>

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">


<title>Survey</title>

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/bootstrap.min.js"></script>



<link href="../styles/survey.css" rel="stylesheet" type="text/css">

<link href="../styles/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("button").click(function () {
            var frame = $('iframe', window.parent.document);
            var height = jQuery(".container").height();
            frame.height(height + 15);
        });
    });
</script>

<script>
function resize(){
  var frame = $('iframe', window.parent.document);
  var height = jQuery(".container").height();
  frame.height(height + 15);
}
</script>

<script>

$(document).ready(function(){

  $('#stepOne form').submit(function (e) {
	  e.preventDefault();
	  $('#stepOne #submit').button('loading');
      $.post('stepSubmit.php?step=1', $('#stepOne form').serialize(), function(data){
        $('head').append(data);
        $('#stepOne').hide('slow');
        $('#stepTwo').slideDown('slow', function(){
          resize();
        });
	    });
    });

  

   $('#stepTwo #submit').click(function (e) {

	  e.preventDefault();

	  $('#stepTwo #submit').button('loading');

    $.post('stepSubmit.php?step=2&session_token='+session_token, $('#stepTwo form').serialize(), function(data){

        $('#stepTwo').hide('slow', function(){

        $('#stepThree').slideDown('slow');

      });

    });

  });

});

</script>

</head>



<body>

	<div class="container">
        <div id="stepTwo">
          	<h1>Questions</h1>

        	<form>

            
            <?php 
              //Connect to DB and get questions
              
              $result = mysqli_query($con, "SELECT * FROM questions");
              while($row = mysqli_fetch_array($result)){
                $choices = json_decode($row['choices']);
                //print_r($choices);
                switch($row['type']){
                  case 'text':
                    echo "<div class='form-group col-sm-12'>

                              <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                              <input type='text' class='form-control' name='{$row[id]}' required placeholder='Text response' />

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
                  default:
                    echo "<h5>unknown question type</h5>";
                }
              }
            ?>
            
            <div class="form-group col-sm-12">

                  <button type="button" id="submit" data-loading-text="Submitting..." class="btn btn-default" autocomplete="off">

                  Finish >>

                </button>

             </div>

            </form>

        </div>

        

        <div id="stepThree" style="display:none">
          	<h1>Thank you!</h1>

            <p>Thank you for taking the survey. Your responses have been recorded.</p>

            <hr>

        </div>

    </div>

</body>

</html>
