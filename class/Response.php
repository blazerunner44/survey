<?php
require_once __DIR__ . '/Model.php';

class Response extends Model{
	public $question_id;
	public $response; 
	public $session_token;

	const tableName = 'results';

	const CHOICE_YES = 'Yes';
	const CHOICE_NO = 'No';

	public function __construct($question_id, $response, $session_token){
		$this->question_id = $question_id;
		$this->response = $response;
		$this->session_token = $session_token;
	}

	
}