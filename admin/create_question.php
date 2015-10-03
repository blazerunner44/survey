<?php
require('../mysql.php');
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

//Convert choices to JSON
$choices=json_encode($_POST['choices']);
print_r($_POST);

mysqli_query($con, "INSERT INTO questions (question,type,description,choices) VALUES ('$_POST[name]','$type', '$_POST[description]','$choices')") or die(mysqli_error($con));
?>