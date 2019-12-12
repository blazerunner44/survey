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
?>

<html>
	<head>
		<script src="../includes/jquery.min.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<title>Survey Results</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
		<script type="text/javascript" src="../includes/vue.js"></script>
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

	</head>
	<body>
		<div id="app" class="containter-fluid">
			<div class="row" v-for="(question, index) in questions">
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					<div class="card border-secondary mb-3">
					  <div class="card-header">{{ question.title }} <small>{{ fullType(question.type) }}</small></div>
					  <div class="card-body">
					    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
					  </div>
					</div>
				</div>
				<div class="col-sm-3"></div>
			</div>

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
		</div>
		<script>
		var app = new Vue({
        	el: '#app',
        	data: {
        		questions: <?php echo json_encode($questions); ?>
        	},
        	computed: {
        		
        	},
        	methods: {
        		fullType: function(input){
					switch(input){
						case '<?=Question::TYPE_YESORNO?>':
						 	return 'Yes or No';
						case '<?=Question::TYPE_OPTION?>':
							return "Multiple Choice";
						case '<?=Question::TYPE_EXPANDED_OPTION?>':
							return 'Multiple Choice (expanded)';
						case '<?=Question::TYPE_SLIDER?>':
							return 'Slider';
						case '<?=Question::TYPE_TEXT?>':
							return 'Text Box';
						case '<?=Question::TYPE_PARAGRAPH?>':
							return 'Paragraph';
						case '<?=Question::TYPE_CHECKBOX?>':
							return 'Checkbox';
						default:
							return 'unknown question type';
					}
				}
        	}
	    });
		</script>

	</body>
</html>