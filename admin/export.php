<?php
header('Content-type: text/csv');
header('Content-Disposition: attachment;filename=results.csv');
session_start();
require '../class/Survey.php';
require '../class/Question.php';

$survey = new Survey();

if($_SESSION['auth'] != True){
	header('Location: index.php');
	exit;
}

$out = fopen('php://output', 'w');

$con = Model::getConnection();

$questionMap = array();
$questionText = array();
foreach($survey->getQuestions() as $question){
	array_push($questionMap, $question->pk);
	array_push($questionText, $question->title);
}

fputcsv($out, $questionText);

$query = mysqli_query($con, "SELECT DISTINCT session_token FROM results");
while($tokenRow = mysqli_fetch_assoc($query)){
	$csvRow = array_fill(0, count($questionMap), '--');
	$sessionQuery = mysqli_query($con, "SELECT * FROM results WHERE session_token = '" . $tokenRow['session_token'] . "'");
	while($row = mysqli_fetch_assoc($sessionQuery)){
		$csvRow[array_search($row['question_id'], $questionMap)] = $row['response'];
	}
	fputcsv($out, $csvRow);
}


fclose($out);