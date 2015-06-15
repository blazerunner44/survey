<?php
require('mysql.php');

$id_esc = mysqli_escape_string($con,$_POST['id']);

mysqli_query($con, "DELETE FROM questions where id='$id_esc'");
mysqli_query($con, "DELETE FROM results WHERE question_id='$id_esc'");
?>