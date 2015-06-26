<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1); 
class Question {
    public $name = "";
    public $description = "";
    public $id = "";
    public $pos  = "";
    public $options = "";
}
require('mysql.php');

$query = mysqli_query($con, "SELECT * FROM questions WHERE id='$_REQUEST[questionId]'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);
// print_r($row);

 
$question = new Question();
$question->name = $row['question'];
$question->description = $row['description'];
$question->pos  = $row['pos'];
$question->id = $row['id'];
 
// Returns: {"firstname":"foo","lastname":"bar"}
json_encode($question);
 
$question->options = $row['choices'];
json_encode($question->options);

 
/* Returns:
    {
        "firstname":"foo",
        "lastname":"bar",
        "birthdate": {
            "date":"2012-06-06 08:46:58",
            "timezone_type":3,
            "timezone":"Europe\/Berlin"
        }
    }
*/
$new = json_encode($question,JSON_PRETTY_PRINT);
// print_r($question);
echo $new;

?>
