<?php 
require('mysql.php');
?>

<html>
	<head>
		<script src="../js/jquery.min.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<title>Survey Results</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="http://bootswatch.com/sandstone/bootstrap.min.css">
		<style>
		/* Bellow is the styling for the title of the page. This is not required...*/
		.title{
			font-weight: bolder;
			font-size: 500%;
			margin-bottom: 30px;
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
		</style>

		<!-- Styles for "NEW" button -->
	    <script src="new_button/js/prefixfree.min.js"></script>
	    <script src="new_button/js/modernizr.js"></script>
		<link type="text/css" rel="stylesheet" href="new_button/css/normalize.css" />
		<link type="text/css" rel="stylesheet" href="new_button/css/style.css" />

	</head>
	<body>
		<div class="containter-fluid">
			<div class="row">
				<div class="col-xs-12 col-md-8 col-md-offset-2">
					<div class="row panel panel-default">
						<div class="col-xs-12 col-md-10 col-md-offset-1">
							<h1 class="title">Survey Results</h1>
							<!-- Single button -->
							
							<div class="btn-group">
							  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style='margin:-20px 0 10px 0'>
							    <?php if(isset($_GET['houseStreet'])){echo $_GET['houseStreet'] . " <span class='caret'></span>";}else{ echo "Filter by Street <span class='caret'></span>";}?>
							  </button>
							  <ul class="dropdown-menu" role="menu">
							  	<?php $query=mysqli_query($con, "SELECT id,houseStreet FROM takers");
							  	$result_array=array();
								while($row=mysqli_fetch_array($query)){
									if (!array_key_exists($row['houseStreet'], $result_array)) {
											$result_array[$row['houseStreet']] = 1;
											echo " <li><a href='?houseStreet={$row[houseStreet]}'>{$row[houseStreet]}</a></li>";
										}
								   
								 } ?>
								 <li class="divider"></li>
								 <li><a href="?">All Streets</a></li>
							  </ul>
							</div>
						</div>

					<?php
					$question_query = mysqli_query($con, "SELECT * FROM questions ORDER BY id ASC");
					while($row = mysqli_fetch_array($question_query)){?>
						<div class="col-xs-12 col-md-10 col-md-offset-1">
							<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							  	<div class="panel panel-default">
							    	<div class="panel-heading" role="tab" id="headingOne">
							      		<h4 class="panel-title">
							        		<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href='<?php echo "#collapse{$row[id]}"; ?>' aria-expanded="false" aria-controls=<?php echo "collapse{$row[id]}"; ?>>
							          			<span class="glyphicon glyphicon-chevron-down" aria-hidden="false">&nbsp;&nbsp; <?php echo "{$row[question]}"; ?></span>
							        		</a>
							        		<span class="glyphicon glyphicon-remove remove" style="float:right;" id='<?php echo "{$row[id]}"; ?>'></span>
							      		</h4>
							    	</div>
									
									<?php
									//Calculate % for each question
									if (isset($_GET['houseStreet'])){
										$results = mysqli_query($con, "SELECT results.* FROM results WHERE question_id='$row[id]' AND results.session_token in ( select takers.session_token from takers where takers.houseStreet='$_GET[houseStreet]') ORDER BY id ASC");
									}else {
										$results=mysqli_query($con, "SELECT * FROM results WHERE question_id='$row[id]'");
									}
									
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
							    	<div id=<?php echo "collapse{$row[id]}"; ?> class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							    		<?php if($count==0){echo "<div class='panel-body'><h3>No Responses Recorded</h3></div>";} ?>
							    		<?php foreach($result_array as $response=>$number){?>
							      		<div class="panel-body">
							      			<h3><?php echo $response; ?></h3>
							      			
							        		<div class="progress">
							        		<!--

							        		-->

				  								<div class="progress-bar progress-bar-<?php if(strtolower($response) == "no"){echo "danger";}else{echo "success";}?> progress-bar-striped" role="progressbar" aria-valuenow='<?php echo ($number/$count)*100 ."%" ?>' aria-valuemin="0" aria-valuemax="100" style='width: <?php echo ($number/$count)*100 ."%" ?>'>
				    								<?php echo round(($number/$count)*100) ."%  ({$number} votes)" ?>
				  								</div>
											</div>
										</div>
										<?php } ?>
										
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
										      				<option>Yes or No</option>
										      				<option>Multiple Choice</option>
										      				<option>Multiple Choice (expanded)</option>
										      			</select>
										      		</div>
										      		<hr><h4>Choices</h4>
										      		<div id="choices">
											      		<div class="form-group">
											      			<input type="text" name="choices[]" class="form-control" placeholder='Enter a Choice' />
										      			</div>
										      		</div>
										      		<span id='new_choice' class='add-new'></span> Add New Choice<br><br>
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
					alert(data);
					location.reload();
				});
				
			});
		});
		</script>
	</body>
</html>
<?php COUCH::invoke(); ?>