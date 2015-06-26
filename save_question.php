<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1); 
class Question {
    public $name = '';
    public $description = '';
    public $pos  = '';
    public $id = '';
    public $options = '';

    function __construct($name,$description,$pos,$options,$id) {           
        $this->name = $name;
        $this->description = $description;
        $this->pos = $pos; 
        $this->id = $id;
        $this->options = $options;          
    }               
}
require('mysql.php');

$question = new Question($_POST['questionName'],$_POST['questionDescription'],$_POST['questionPos'],json_encode($_POST['questionOption']),$_POST['questionId']);

var_dump($question);
mysqli_query($con, "UPDATE questions SET question='$question->name', description='$question->description', choices='$question->options', pos='$question->pos' WHERE id='$question->id'");

?>
