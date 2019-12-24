<?php
require_once '../class/Question.php';
if(empty($_POST['id'])){
	echo "Question id required";
	exit;
}else{
	$id = (int) $_POST['id'];
}

$question = Question::getByPkEqual($id);

$question->title = $_POST['title'];
$question->description = $_POST['description'];
$question->choices = json_decode($_POST['choices']);

if($question->save()){
	echo 'success';
}else{
	echo 'error';
}
?>
