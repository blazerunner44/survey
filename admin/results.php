<?php 
session_start();
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
			<div class="row" style="margin-bottom: 15px;">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<h1>{{ surveyTitle }}</h1>
					<a href="../index.php" target="_blank">View Survey</a><a href="export.php" style="margin-left:15px">Download Results</a><a href="logout.php" style="float:right;">Logout</a><a href="#settings" style="float:right;margin-right:50px;" onclick="$('#settings').show();$('html').css('overflow', 'hidden');">Settings</a></h1>
				</div>
				<div class="col-md-3"></div>
			</div>
			<div class="row" v-for="(question, index) in questions">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="card border-secondary mb-3">
					  <div class="card-header">Question {{ index+1 }} <small>{{ fullType(question.type) }}</small><button v-on:click="editQuestion(index)" class="btn btn-primary float-right">Edit</button><button v-on:click="deleteQuestion(index)" class="btn btn-danger float-right" style="margin-right:10px">Delete</button></div>
					  <div class="card-body">
					    <template v-if="question.type == '<?=Question::TYPE_YESORNO?>' || question.type == '<?=Question::TYPE_OPTION?>' || question.type == '<?=Question::TYPE_EXPANDED_OPTION?>' || question.type == '<?=Question::TYPE_CHECKBOX?>'">
					    	<h4>{{ question.title }}</h4>
					    	<canvas :id="question.pk" v-bind:index="index" class="pieChart"></canvas>
					    </template>
					    <template v-if="question.responses.length < 1">
					    	<p>No Responses</p>
					    </template>
					    <template v-else-if="question.type == '<?=Question::TYPE_SLIDER?>'">
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
					    <template v-else-if="question.type == '<?=Question::TYPE_TEXT?>' || question.type == '<?=Question::TYPE_PARAGRAPH?>'">
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

			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="card border-secondary mb-3">
					  <div class="card-header">New Question</div>
					  <div class="card-body">
					    <form id="newQuestionForm">
					    	<div class="form-group">
					    		<label>Question Title</label>
					    		<input type="text" class="form-control" name="title" v-model="newQuestionTitle">
					    	</div>
					    	<div class="form-group">
					    		<label>Question Description</label>
					    		<input type="text" class="form-control" name="description" v-model="newQuestionDescription">
					    	</div>
					    	<div class="form-group">
					    		<label>Question Type</label>
					    		<select class="form-control newQuestionType" name="type" v-model="newQuestionType">
					    			<option value="<?=Question::TYPE_YESORNO?>"></option>
					    			<option value="<?=Question::TYPE_OPTION?>"></option>
					    			<option value="<?=Question::TYPE_EXPANDED_OPTION?>"></option>
					    			<option value="<?=Question::TYPE_CHECKBOX?>"></option>
					    			<option value="<?=Question::TYPE_SLIDER?>"></option>
					    			<option value="<?=Question::TYPE_TEXT?>"></option>
					    			<option value="<?=Question::TYPE_PARAGRAPH?>"></option>
					    		</select>
					    	</div>
					    	<div class="form-group" style="width:90%; margin-left:5%" v-if="newQuestionType == '<?=Question::TYPE_OPTION?>' || newQuestionType == '<?=Question::TYPE_EXPANDED_OPTION?>' || newQuestionType == '<?=Question::TYPE_CHECKBOX?>'">
					    		<label>Choices</label>
					    		<div v-for="(choice, choiceIndex) in newQuestionChoices" style="margin-bottom:25px">
					    			<input type="text" class="form-control" style="width:85%; float:left;" v-model="newQuestionChoices[choiceIndex]" placeholder="Choice"><a href="javascript:void(0);" v-on:click="removeNewQuestionChoice(choiceIndex)" style="float:left; margin-left:1%">Remove</a><br>
					    		</div>
					    		<br>
					    		<a href="javascript:void(0);" style="clear:left" v-on:click="addNewQuestionChoice()">+ Add Choice</a>
					    	</div>

					    	<div style="width:90%; margin-left:5%" v-if="newQuestionType == '<?=Question::TYPE_SLIDER?>'">
					    		<div class="form-group">
						    		<label>Min Value</label>
						    		<input type="text" class="form-control" v-model="newQuestionChoices[0]" placeholder="Min Value"><br>
					    		</div>
					    		<div class="form-group">
						    		<label>Max Value</label>
						    		<input type="text" class="form-control" v-model="newQuestionChoices[1]" placeholder="Max Value"><br>
					    		</div>
					    	</div>

					    	<button type="button" class="btn btn-primary float-right" v-on:click="createQuestion()">Create Question >></button>
					  	</form>
					  </div>
					</div>
				</div>
				<div class="col-md-3"></div>
			</div>

			<div id="settings" class="container">
				<a id="closeSettings" onclick="$('#settings').hide();$('html').css('overflow', 'scroll');">X</a>
				<div class="row">
					<div class="col-sm-4"></div>
					<div class="col-sm-6" style="text-align:center">
						<h1>Settings</h1>
						<hr>
						<form action="updateSettings.php" method="POST">
							<input name="name" class="form-control" placeholder="Survey Name" value="<?= $survey->name; ?>"/>
							<input name="email" class="form-control" placeholder="Survey Email" value="<?= $survey->email; ?>" />
							<textarea name="description" class="form-control" placeholder="Survey Description" rows="10"><?php echo str_replace('<br />', '', $survey->description); ?></textarea>
							<button type="submit" class="btn btn-primary" style="float:right"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
						</form>
					</div>
				</div>
			</div>

			<div class="modal" id="editQuestion" tabindex="-1" role="dialog">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">Edit Question</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <form id="newQuestionForm">
					    	<div class="form-group">
					    		<label>Question Title</label>
					    		<input type="text" class="form-control" name="title" v-model="tempEditQuestion.title">
					    	</div>
					    	<div class="form-group">
					    		<label>Question Description</label>
					    		<input type="text" class="form-control" name="description" v-model="tempEditQuestion.description">
					    	</div>
					    	<div class="form-group">
					    		<label>Question Type</label>
					    		<select class="form-control newQuestionType" disabled name="type" v-bind:value="tempEditQuestion.type">
					    			<option value="<?=Question::TYPE_YESORNO?>"></option>
					    			<option value="<?=Question::TYPE_OPTION?>"></option>
					    			<option value="<?=Question::TYPE_EXPANDED_OPTION?>"></option>
					    			<option value="<?=Question::TYPE_CHECKBOX?>"></option>
					    			<option value="<?=Question::TYPE_SLIDER?>"></option>
					    			<option value="<?=Question::TYPE_TEXT?>"></option>
					    			<option value="<?=Question::TYPE_PARAGRAPH?>"></option>
					    		</select>
					    	</div>
					    	<div class="form-group" style="width:90%; margin-left:5%" v-if="tempEditQuestion.type == '<?=Question::TYPE_OPTION?>' || tempEditQuestion.type == '<?=Question::TYPE_EXPANDED_OPTION?>' || tempEditQuestion.type == '<?=Question::TYPE_CHECKBOX?>'">
					    		<label>Choices</label>
					    		<div v-for="(choice, choiceIndex) in tempEditQuestion.choices" style="margin-bottom:25px">
					    			<input type="text" class="form-control" style="width:85%; float:left;" v-model="tempEditQuestion.choices[choiceIndex]" placeholder="Choice"><a href="javascript:void(0);" v-on:click="removeEditQuestionChoice(choiceIndex)" style="float:left; margin-left:1%">Remove</a><br>
					    		</div>
					    		<br>
					    		<a href="javascript:void(0);" style="clear:left" v-on:click="addEditQuestionChoice()">+ Add Choice</a>
					    	</div>
					    	<div style="width:90%; margin-left:5%" v-if="tempEditQuestion.type == '<?=Question::TYPE_SLIDER?>'">
					    		<div class="form-group">
						    		<label>Min Value</label>
						    		<input type="text" class="form-control" v-model="tempEditQuestion.choices[0]" placeholder="Min Value"><br>
					    		</div>
					    		<div class="form-group">
						    		<label>Max Value</label>
						    		<input type="text" class="form-control" v-model="tempEditQuestion.choices[1]" placeholder="Max Value"><br>
					    		</div>
					    	</div>
					  	</form>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			        <button type="button" class="btn btn-primary" v-on:click="saveEditQuestion()">Save changes</button>
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
        		newQuestionTitle: '',
        		newQuestionDescription: '',
        		newQuestionType: '<?=Question::TYPE_YESORNO?>',
        		newQuestionChoices: [''],
        		tempEditIndex: 0,
        		tempEditQuestion: {
        			'title': '',
        			'description': '',
        			'choices': [],
        		}
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
        		},
        		createQuestion: function(){
        			var newQuestion = {
        				'title': this.newQuestionTitle,
        				'description': this.newQuestionDescription,
        				'type': this.newQuestionType,
        				'choices': JSON.stringify(this.newQuestionChoices),
        				'responses': []
        			}

        			const formData = new FormData();
				    Object.keys(newQuestion).forEach(key => formData.append(key, newQuestion[key]));
        			var vue = this;
        			fetch('create_question.php', {
						method: 'post',
						body: formData
					}).then(function(){
						vue.questions.push(newQuestion);
						vue.newQuestionTitle = '';
						vue.newQuestionDescription = '';
						vue.newQuestionType = 'yn';
						vue.newQuestionChoices = [''];
					})
        		},
        		addNewQuestionChoice: function(){
        			this.newQuestionChoices.push('');
        		},
        		removeNewQuestionChoice: function(index){
        			this.newQuestionChoices.splice(index, 1);
        		},
        		addEditQuestionChoice: function(){
        			this.questions[this.editQuestionIndex].choices.push('');
        		},
        		removeEditQuestionChoice: function(index){
        			this.questions[this.editQuestionIndex].choices.splice(index, 1);
        		},
        		editQuestion: function(index){
        			this.tempEditIndex = index;
        			this.tempEditQuestion = JSON.parse(JSON.stringify(this.questions[index]));
        			$("#editQuestion").modal("show");
        		},
        		saveEditQuestion: function(){
        			var newQuestion = {
        				'id': this.tempEditQuestion.pk,
        				'title': this.tempEditQuestion.title,
        				'description': this.tempEditQuestion.description,
        				'type': this.tempEditQuestion.type,
        				'choices': JSON.stringify(this.tempEditQuestion.choices),
        				'responses': []
        			}

        			const formData = new FormData();
				    Object.keys(newQuestion).forEach(key => formData.append(key, newQuestion[key]));
        			var vue = this;
        			fetch('update_question.php', {
						method: 'post',
						body: formData
					})
					.then((resp) => resp.text())
					.then(function(data){
						if(data == 'success'){
							vue.questions[vue.tempEditIndex].title = vue.tempEditQuestion.title;
							vue.questions[vue.tempEditIndex].description = vue.tempEditQuestion.description;
							vue.questions[vue.tempEditIndex].type = vue.tempEditQuestion.type;
							vue.questions[vue.tempEditIndex].choices = vue.tempEditQuestion.choices;
							$("#editQuestion").modal("hide");
						}else{
							alert('Error updating question! ');
						}
					})
        		},
        		deleteQuestion: function(index){
        			if(!confirm("Are you sure you want to delete this question and all associated responses?")){
        				return false;
        			}
        			var form = {
        				'id': this.questions[index].pk,
        			}

        			const formData = new FormData();
				    Object.keys(form).forEach(key => formData.append(key, form[key]));
        			var vue = this;
        			var questionIndex = index;
        			fetch('delete_question.php', {
						method: 'post',
						body: formData
					})
					.then((resp) => resp.text())
					.then(function(data){
						if(data == 'success'){
							vue.questions.splice(questionIndex, 1);
						}else{
							alert('Error deleting question! ');
						}
					})
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

				//Fill in new question types
				var selects = document.getElementsByClassName("newQuestionType");
				for (var i = 0; i < selects.length; i++) {
					var options = selects[i].children;
					for (var j = 0; j < options.length; j++) {
						options[j].innerHTML = this.fullType(options[j].getAttribute('value'));
					};
				};
        	}
	    });
		</script>
	</body>
</html>