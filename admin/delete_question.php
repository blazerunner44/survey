<?php
require_once '../class/Question.php';
if(empty($_POST['id'])){
	echo "Question id required";
	exit;
}else{
	$id = (int) $_POST['id'];
}

$question = Question::getByPkEqual($id);

if($question->delete()){
	echo 'success';
}else{
	echo 'error';
}
?>