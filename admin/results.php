<?php 
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1); 
session_start();
require_once '../mysql.php';
require '../class/Survey.php';
require '../class/Question.php';


if($_SESSION['auth'] != True){
	header('Location: index.php');
	exit;
}
$survey = new Survey();
$questions = $survey->getQuestions();

function fullType($input){
	switch($input){
		case Question::TYPE_YESORNO:
		 	$type = 'Yes or No';
			break;
		case Question::TYPE_OPTION:
			$type = "Multiple Choice";
			break;
		case Question::TYPE_EXPANDED_OPTION:
			$type = 'Multiple Choice (expanded)';
			break;
		case Question::TYPE_SLIDER:
			$type = 'Slider';
			break;
		case Question::TYPE_TEXT:
			$type = 'Text Box';
			break;
		case Question::TYPE_PARAGRAPH:
			$type = 'Paragraph';
			break;
		case Question::TYPE_CHECKBOX:
			$type = 'Checkbox';
			break;
		default:
			$type='unknown question type';
	}
	return $type;
}
?>

<html>
	<head>
		<script src="../includes/jquery.min.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<title>Survey Results</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="http://bootswatch.com/yeti/bootstrap.min.css">
		<!-- <link rel="stylesheet" href="https://bootswatch.com/sandstone/bootstrap.min.css"> -->
		<style>
		/* Bellow is the styling for the title of the page. This is not required...*/
		.title{
			font-weight: bolder;
			font-size: 500%;
			margin-bottom: 30px;
		}
		.containter-fluid{
			margin-top:20px;
		}
		h1 a{
			text-decoration: none;
			font-size:18px;
		}
		span {
			font-size: 18px;
			word-spacing: -12;
			text-transform: none;
		}
		/*Prevents the horizontal scroll bar (this is a bootstrap bug)*/
		html, body {
			overflow-x: hidden;
		}
		#new_question{
			display:none;
		}
		.questionOption{
			margin-bottom:10px;
		}
		.showMore, .showLess{
			cursor: pointer;
		}
		#new_choice{
			cursor: pointer;
		}
		#settings{
			display:none;
			width:100%;
			height:100%;
			position: absolute;
			top: 0;
			left: 0;
			background-color: rgba(255,255,255,0.95);
		}
		#settings input, #settings textarea, #settings button{
			margin-bottom: 10px;
		}
		#closeSettings{
			font-size:32pt;
			font-weight: lighter;
			position: fixed;
			right: 20px;
			color: black;
		    text-decoration: none;
		    cursor: pointer;
			z-index: 10;
		}
		</style>

		<!-- Styles for "NEW" button -->
	    <script src="../new_button/js/prefixfree.min.js"></script>
	    <script src="../new_button/js/modernizr.js"></script>
		<link type="text/css" rel="stylesheet" href="../new_button/css/normalize.css" />
		<link type="text/css" rel="stylesheet" href="../new_button/css/style.css" />

<script>
$(document).ready(function(){
			var choices = $("#choices");
			var sliderChoices = $("#sliderChoices");

			$('#new_choice').click(function(){
				$(this).before('<div class="form-group"> <input type="text" name="choices[]" class="form-control" placeholder="Enter a Choice" /> </div>');
			});

			$("#choices").detach();
			$("#sliderChoices").detach();

		$( "select" )
		  .change(function () {
		    var str = "";
		    if ($("select option:selected").hasClass('nochoice')){
		    	$("#choices").detach();
		    	$("#sliderChoices").detach();
		    }else if ($("select option:selected").hasClass('slider')){
		    	$("#allChoices").append(sliderChoices);
		    	$("#choices").detach();
		    }else if ($("select option:selected").hasClass('choices')){
		    	$("#sliderChoices").detach();
		    	$("#allChoices").append(choices);
		    }
		  })
		  .change();
		});
</script>

  <script>
  $(document).ready(function() {
    $( ".panel" ).each(function( index ) {
	  $(this).find('.level').html(index+1);
	});

	$('.edit').click(function(){
		var editId = this.id;
		$.post('load_question.php', {"questionId": editId}, function(data){
			$('#options').empty();
			// alert(data);
			data = JSON.parse(data);
			// alert(data.options);
			data.options = JSON.parse(data.options);
			// alert(typeof(data.options));
			// alert(data);
			$('#questionName').val(data.name);
			$('#questionDescription').val(data.description);
			$('#myModalLabel').text(data.name);
			$('#questionPos').val(data.pos);
			$('#editQuestionId').val(data.id);
			for (var i = 0; i < data.options.length; i++) {
				$('#options').append('<input type="text" class="form-control questionOption" name="questionOption[]" value="' + data.options[i] +'">');
			}
			// $.each(data.options, function( i, l ){
			//   $('#options').append('<input type="text" class="form-control" id="questionOption" name="questionOption[]" value="' + l +'">');
			// });
			// location.reload();
		});
		var updateQuestion = true;
		$('#editQuestion').modal();
	});

	$('#saveEdit').click(function(){
		$.post('save_question.php', $('#editQuestionForm').serialize(), function(data){
			//alert(data);
			location.reload();
		});
	});
  });
</script>
	</head>
	<body>
		<!-- Modal -->
<div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
      <form id="editQuestionForm">
      	<input type="hidden" name="questionId" id="editQuestionId" />
		  <div class="form-group">
		    <label for="questionName">Question Name</label>
		    <input type="text" class="form-control" id="questionName" name="questionName">
		  </div>
		  <div class="form-group">
		    <label for="questionDescription">Question Description</label>
		    <input type="text" class="form-control" id="questionDescription" name="questionDescription">
		  </div>
		  <div class="form-group">
		    <label for="questionPos">Question Position</label>
		    <input type="text" class="form-control" id="questionPos" name="questionPos">
		  </div>
		  <div class="form-group">
		    <label for="questionOption">Question Options</label>
		    <div id="options"></div>
		  </div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="saveEdit" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
		<!-- Small modal -->
		<div class="containter-fluid">
			<div class="row">
				<div class="col-xs-12 col-md-8 col-md-offset-2">
					<h1 class="col-md-offset-1 col-md-10 col-xs-12"><?php echo $survey->name;?> &nbsp; <a href="../index.php" target="_blank">View Survey</a><a href="logout.php" style="float:right;">Logout</a><a href="#settings" style="float:right;margin-right:50px;" onclick="$('#settings').show();$('html').css('overflow', 'hidden');">Settings</a></h1>

					<?php
					foreach($questions as $question){
					?>
						<div class="col-xs-12 col-md-10 col-md-offset-1">
							<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							  	<div class="panel panel-default">
							    	<div class="panel-heading" role="tab" id="headingOne">
							      		<h4 class="panel-title">
							        		<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href='collapse<?=$question->pk?>' aria-expanded="false" aria-controls='collapse<?=$question->pk?>'>
							          			<span class="glyphicon glyphicon-chevron-down" aria-hidden="false">&nbsp;&nbsp; <?=$question->title?> <small><i><?=fullType($question->type)?></i></small></span>
							        			
							        		</a>

							        		<span class="glyphicon glyphicon-remove remove" style="float:right;" id='<?=$question->pk?>'></span>
							        		<span class="glyphicon glyphicon-pencil edit" style="float:right;" id='<?=$question->pk?>'></span>
							        		<span class="level" style="float:right">Rank</span>
							      		</h4>
							    	</div>
									
									<?php
									//Calculate % for each question
									
									$results=mysqli_query($con, "SELECT * FROM results WHERE question_id='{$question->pk}' ORDER BY id DESC");
									
									$result_array = array();
									$count=0;
									while($result = mysqli_fetch_array($results)){
										$response = $result['response'];
										//echo $response;
										
										if (!array_key_exists(strtoupper($response), $result_array)) {
											$result_array[strtoupper($response)] = 1;
										}else{
											$result_array[strtoupper($response)] = $result_array[strtoupper($response)]+1;
										}
										//echo " | ";
										$count++;
									}
									//print_r($result_array);
									?>
							    	<div id='collapse<?=$question->pk?>' class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							    		<?php 
							    		if($count==0){
							    			echo "<div class='panel-body'><h3>No Responses Recorded</h3></div>";
							    		} 

							    		$hideCount = 0;

							    		foreach($result_array as $response=>$number){?>
								    		<?php 
								      			if($hideCount == 5){
								      				echo "<div class='panel-body showMore'><span class='glyphicon glyphicon-chevron-down'></span></div>";
								      			}
								      		?>
								      		<div class="panel-body" <?php if($hideCount >= 5){ echo 'style="display:none"';} ?> >
								      			<h3>
								      				<?php 
								      					echo stripslashes($response);
								      					$hideCount++; 
								      				?>
								      			</h3>
								      			<?php if ($question->type != Question::TYPE_PARAGRAPH and $question->type != Question::TYPE_TEXT){?>
								      				
								        		<div class="progress">
					  								<div class="progress-bar progress-bar-<?php if(strtolower($response) == "no"){echo "danger";}else{echo "success";}?> progress-bar-striped" role="progressbar" aria-valuenow='<?php echo ($number/$count)*100 ."%" ?>' aria-valuemin="0" aria-valuemax="100" style='width: <?php echo ($number/$count)*100 ."%" ?>'>
					    								<?php echo round(($number/$count)*100) ."%  ({$number} votes)" ?>
					  								</div>
												</div>
												<?php }?>
											</div>
										<?php } ?>
										<div class='panel-body showLess' style="display:none"><span class='glyphicon glyphicon-chevron-up'></span></div>
							    	</div>
							    	
							  	</div>
							</div>
						</div>
					<?php } ?>
						<div class="col-xs-12 col-md-10 col-md-offset-1">
							<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							  	<div class="panel panel-default">
							    	
							    	<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							      		<div class="panel-body">
							      			<span class="add-new" id="add_question_plus" style="width:55px;height:55px;"></span>
							      			<div id="new_question">
							      				<h3>Create Question</h3>
							      				<form>
								      				<div class="form-group">
										      			<input type="text" name="name" id="name" class="form-control" placeholder="Question Name" />
									      			</div>

									      			<div class="form-group">
										      			<input type="text" name="description" id="description" class="form-control" placeholder="Question Description" />
									      			</div>

									      			<div class="form-group">
										      			<select class="form-control" name="type">
										      				<option disabled selected="selected">Question Type</option>
										      				<option id="yn" class="nochoice">Yes or No</option>
										      				<option class='nochoice'>Text Box</option>
										      				<option class='nochoice'>Paragraph</option>
										      				<option class='choices'>Multiple Choice</option>
										      				<option class='choices'>Multiple Choice (expanded)</option>
										      				<option class='choices'>Checkbox</option>
										      				<option class="slider">Slider</option>
										      			</select>
										      		</div>
										      		<hr>
										      		<div id="allChoices">
											      		<div id="choices">
											      			<h4>Choices</h4>
												      		<div class="form-group">
												      			<input type="text" name="choices[]" class="form-control" placeholder='Enter a Choice' />
											      			</div>
											      			<div id='new_choice'> <span class='add-new'></span> Add New Choice</div>
											      		</div>

											      		<div id="sliderChoices">
											      			<h4>Slider Values</h4>
											      			<div class="form-group">
												      			<input type="number" name="choices[]" class="form-control" placeholder='Min Value' />
												      			<input type="number" name="choices[]" class="form-control" placeholder='Max Value' />
											      			</div>
											      		</div>
										      		</div>
									      			<button type="button" class="btn btn-primary" id="new_submit">Add Question</button>
									      		</form>
								      		</div>
										</div>
							    	</div>
							  	</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<script>
		//
		//Make the questions uncollapsed when the page is loaded.
		//
		$(document).ready(function(){
			$(".collapse").collapse("show");
			$(".collapse").click(function(){
				$(this).collapse('toggle');
			});

			$('.remove').click(function(){
				if(confirm("Are you sure you want to delete this question? All responses will be deleted as well!")){
					$(this).parent().parent().parent().slideUp();
					$.post('remove_question.php', {id:$(this).attr('id')}, function(data){

					});
				}
			});
			$("#add_question_plus").click(function(){
				$('#new_question').slideToggle();
			});

			$('#new_choice').click(function(){
				$('#choices').append('<div class="form-group"> <input type="text" name="choices[]" class="form-control" placeholder="Enter a Choice" /> </div>');
			});

			$('#new_submit').click(function(){
				$.post('create_question.php', $('form').serialize(), function(data){
					// alert(data);
					location.reload();
				});
				
			});

			$('.showMore').click(function(){
				//alert('success');
				$(this).siblings().slideDown();
				$(this).hide();
				$(this).siblings('.showLess').show();
			});

			$('.showLess').click(function(){
				//$(this).siblings().slideUp();
				$(this).siblings('.showMore').show();
				$(this).siblings().slice(6,$(this).siblings().length).slideUp();
				$(this).hide();
			});
		});
		</script>

		<div id="settings" class="container">
			<a id="closeSettings" onclick="$('#settings').hide();$('html').css('overflow', 'scroll');">X</a>
			<div class="col-sm-12 col-md-10 col-md-offset-2">
				<h1>Settings</h1>
				<hr>
				<form action="updateSettings.php" method="POST">
					<input name="name" class="form-control" placeholder="Survey Name" value="<?= $survey->name; ?>"/>
					<input name="email" class="form-control" placeholder="Survey Email" value="<?= $survey->email; ?>" />
					<textarea name="description" class="form-control" placeholder="Survey Description" rows="10"><?= $survey->description; ?></textarea>
					<button type="submit" class="btn btn-primary" style="float:right"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
				</form>
			</div>
		</div>
	</body>
</html>