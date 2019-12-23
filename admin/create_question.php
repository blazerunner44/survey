<?php
require_once '../class/Question.php';

$question = new Question(NULL, $_POST['title'], $_POST['description'], $_POST['type'], $_POST['pos'], $_POST['choices']);
$question->save();

?>