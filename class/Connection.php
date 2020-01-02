<?php
function connect(){
	return mysqli_connect(
		'localhost', //Database server
		'db_user', //Database user
		'db_pass', //Database password
		'db_name' //Database name
	);
}
?>