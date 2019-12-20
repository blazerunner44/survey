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
		.containter-fluid{
			margin-top:20px;
		}
		h1 a{
			text-decoration: none;
			font-size:18px;
		}
		/*Prevents the horizontal scroll bar (this is a bootstrap bug)*/
		html, body {
			overflow-x: hidden;
		}
		h4{
			text-align: center;
			margin-bottom: 20px;
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
		.textResponseCard{
			font-size: 12pt;
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
					  <div class="card-header">Question {{ index+1 }} <small>{{ fullType(question.type) }}</small></div>
					  <div class="card-body">
					    <template v-if="question.type == '<?=Question::TYPE_YESORNO?>' || question.type == '<?=Question::TYPE_OPTION?>' || question.type == '<?=Question::TYPE_EXPANDED_OPTION?>'">
					    	<h4>{{ question.title }}</h4>
					    	<canvas :id="question.pk" v-bind:index="index" class="pieChart"></canvas>
					    </template>
					    <template v-if="question.type == '<?=Question::TYPE_SLIDER?>'">
					    	<h4>{{ question.title }}</h4>
					    	<div class="row" style="text-align:center">
					    		<div class="col-sm-3">
					    			<span>{{ min(index) }}</span>
					    			<h6>MIN</h6>
					    		</div>
					    		<div class="col-sm-6">
					    			<span>{{ mean(index) }}</span>
					    			<h5>AVERAGE</h5>
					    		</div>
					    		<div class="col-sm-3">
					    			<span>{{ max(index) }}</span>
					    			<h6>MAX</h6>
					    		</div>
					    	</div>
					    </template>
					    <template v-if="question.type == '<?=Question::TYPE_TEXT?>' || question.type == '<?=Question::TYPE_PARAGRAPH?>'">
							<h4>{{ question.title }}</h4>
							<div class="card">
								<ul class="list-group list-group-flush" :key="pageKey">
									<li class="list-group-item textResponseCard" v-for="(response, rindex) in getPaginatedResponses(index)">{{ response.response }}</li>
								</ul>
							</div>
							<div style="text-align:center; margin-top: 15px;">
								<button class="btn btn-light" v-on:click="prevPage(index)"><< Prev</button>
								<span style="margin: 0 10px">{{ question.currentPage }} of {{ question.pageCount }}</span>
								<button class="btn btn-light" v-on:click="nextPage(index)">Next >></button>
							</div>
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
        		pageKey: 0,
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
				},
				min: function(question_index){
        			var min = '';
        			for (var i = 0; i < this.questions[question_index].responses.length; i++) {
        				if(min == ''){
        					min = parseInt(this.questions[question_index].responses[i].response);
        				}else{
        					if(min > parseInt(this.questions[question_index].responses[i].response)){
        						min = parseInt(this.questions[question_index].responses[i].response);
        					}
        				}
        			};
        			return min;
        		},
        		max: function(question_index){
        			var max = '';
        			for (var i = 0; i < this.questions[question_index].responses.length; i++) {
        				if(max == ''){
        					max = parseInt(this.questions[question_index].responses[i].response);
        				}else{
        					if(max < parseInt(this.questions[question_index].responses[i].response)){
        						max = parseInt(this.questions[question_index].responses[i].response);
        					}
        				}
        			};
        			return max;
        		},
        		mean: function(question_index){
        			var total = 0.00;
        			for (var i = 0; i < this.questions[question_index].responses.length; i++) {
        				if(!isNaN(this.questions[question_index].responses[i].response) && this.questions[question_index].responses[i].response != ''){
        					total += parseFloat(this.questions[question_index].responses[i].response);
        				}
        			};
        			
        			return (total / this.questions[question_index].responses.length).toFixed(2);
        		},
        		getPaginatedResponses: function(question_index){
        			let currentPage = this.questions[question_index].currentPage;
        			let pageSize = this.questions[question_index].pageSize;

        			let end = currentPage * pageSize;
        			let start = end - pageSize;

        			if(end > this.questions[question_index].responses.length){
        				end = this.questions[question_index].responses.length
        			}

        			return this.questions[question_index].responses.slice(start,end);
        		},
        		nextPage: function(question_index){
        			this.questions[question_index].currentPage++;
        			if(this.questions[question_index].currentPage > this.questions[question_index].pageCount){
        				this.questions[question_index].currentPage = 1;
        			}
        			this.pageKey++;
        		},
        		prevPage: function(question_index){
        			this.questions[question_index].currentPage--;
        			if(this.questions[question_index].currentPage < 1){
        				this.questions[question_index].currentPage = this.questions[question_index].pageCount;
        			}
        			this.pageKey++;
        		}
        	},
        	created: function(){
        		//Setup additional variables for pagination questions
        		const PAGE_SIZE = 5;
        		for (var i = 0; i < this.questions.length; i++) {
        			if(this.questions[i].type == '<?=Question::TYPE_TEXT?>' || this.questions[i].type == '<?=Question::TYPE_PARAGRAPH?>'){
        				this.questions[i].pageSize = PAGE_SIZE;
        				this.questions[i].currentPage = 1;
        				this.questions[i].pageCount = Math.ceil(this.questions[i].responses.length / PAGE_SIZE);
        			}
        		};
        	},
        	mounted: function(){
        		// Setup Pie charts
				var colors = ['#28a745', '#007bff','#333333','#c3e6cb','#dc3545','#6c757d'];

				var chartOptions = {
				  cutoutPercentage: 50, 
				  legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
				};

				var charts = document.getElementsByClassName("pieChart");
				for (var i = 0; i < charts.length; i++) {
					//Get the data from Vue
					var question_index = charts[i].getAttribute('index');
					if(this.questions[question_index].type == '<?= Question::TYPE_YESORNO?>'){
						var data = {'Yes': 0, 'No': 0}; //Sets uniform position/color of yes and no pie slices
					}else{
						var data = {};
					}
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
					      options: chartOptions
					  });
					}
				};
        	}
	    });
		</script>

	</body>
</html>