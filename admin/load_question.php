<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1); 
require('../mysql.php');
require_once('Question.php');

$query = mysqli_query($con, "SELECT * FROM questions WHERE id='$_REQUEST[questionId]'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);
// print_r($row);

 
$question = new Question($row['question'], $row['description'], $row['pos'], $row['choices'], $row['id']);
 
// Returns: {"firstname":"foo","lastname":"bar"}
json_encode($question);
json_encode($question->options);

 
$new = json_encode($question,JSON_PRETTY_PRINT);
// print_r($question);
echo $new;

?>
