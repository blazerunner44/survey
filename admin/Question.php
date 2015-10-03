<?php

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

?>