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
		<script src="../includes/chart.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../includes/chart.min.css">
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

	</head>
	<body>
		<div id="app" class="containter-fluid">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<h1>{{ surveyTitle }} &nbsp; <a href="../index.php" target="_blank">View Survey</a><a href="logout.php" style="float:right;">Logout</a><a href="#settings" style="float:right;margin-right:50px;" onclick="$('#settings').show();$('html').css('overflow', 'hidden');">Settings</a></h1>
				</div>
				<div class="col-md-3"></div>
			</div>
			<div class="row" v-for="(question, index) in questions">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="card border-secondary mb-3">
					  <div class="card-header">{{ question.title }} <small>{{ fullType(question.type) }}</small></div>
					  <div class="card-body">
					    <template v-if="question.type == '<?=Question::TYPE_YESORNO?>'">
					    	Yes or no
					    </template>
					    <template v-if="question.type == '<?=Question::TYPE_OPTION?>' || question.type == '<?=Question::TYPE_EXPANDED_OPTION?>'">
					    	<canvas :id="question.pk" v-bind:index="index" class="pieChart"></canvas>
					    </template>
					  </div>
					</div>
				</div>
				<div class="col-md-3"></div>
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
        		questions: <?php echo json_encode($questions); ?>,
        		surveyTitle: "<?= $survey->name ?>",
        		surveyDescription: "<?= $survey->description ?>",
        		surveyEmail: "<?= $survey->email ?>"
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
        	},
        	mounted: function(){
        		// chart colors
				var colors = ['#007bff','#28a745','#333333','#c3e6cb','#dc3545','#6c757d'];

				/* 3 donut charts */
				var donutOptions = {
				  cutoutPercentage: 50, 
				  legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
				};

				var charts = document.getElementsByClassName("pieChart");
				for (var i = 0; i < charts.length; i++) {
					//Get the data from Vue
					var question_index = charts[i].getAttribute('index');
					var data = {};
					for (var j = 0; j < this.questions[question_index].responses.length; j++) {
						if(data[this.questions[question_index].responses[j].response] == undefined){
							data[this.questions[question_index].responses[j].response] = 1;
						}else{
							data[this.questions[question_index].responses[j].response] += 1;
						}
					};

					var chartData = {
					    labels: Object.keys(data),
					    datasets: [
					      {
					        backgroundColor: colors.slice(0,3),
					        borderWidth: 0,
					        data: Object.values(data)
					      }
					    ]
					};
					if (charts[i]) {
					  new Chart(charts[i], {
					      type: 'pie',
					      data: chartData,
					      options: donutOptions
					  });
					}
				};
        	}
	    });
		</script>

	</body>
</html>