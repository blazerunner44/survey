<?php
require('mysql.php');
mysqli_query($con, "DELETE FROM questions where id='$_POST[id]'");
mysqli_query($con, "DELETE FROM results WHERE question_id='$_POST[id]'");
?>