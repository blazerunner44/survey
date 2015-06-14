<?php
require('mysql.php');
switch($_POST['type']){
	case 'Yes or No':
		$type='yn';
		break;
	case 'Multiple Choice (expanded)':
		$type='expanded_option';
		break;
	case 'Slider':
		$type='slider';
		break;
	case 'Text Box':
		$type='text';
		break;
	case 'Paragraph':
		$type='paragraph';
		break;
	default:
		$type='option';
}
print_r($_POST);
//Convert choices to JSON
if(isset($_POST['choices'])){
$choices=json_encode($_POST['choices']);
}else{
	$choices = '[""]';
}


mysqli_query($con, "INSERT INTO questions (question,type,description,choices) VALUES ('".mysqli_escape_string($_POST[name])."','$type', '".mysqli_escape_string($_POST[description])."','".mysqli_escape_string($choices)."')") or die(mysqli_error($con));
?>