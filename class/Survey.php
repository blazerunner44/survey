<?php
class Survey {
	public $name;
    public $email;
	public $description;

	private $questions = array();
	
	public function __construct() {
		$con = mysqli_connect('localhost','admin_survey','survey','admin_survey');
		$sql = mysqli_query($con, "SELECT * FROM settings");
		while($row = mysqli_fetch_assoc($sql)){
			$this->{$row['name']} = $row['value'];
		}
		
	}

	public static function verifyUser($username, $password){
		$con = mysqli_connect('localhost','admin_survey','survey','admin_survey');
		$escapedUsername = self::escapeInput($username); //Escape username for database query
		$row = mysqli_query($con, "SELECT username, password FROM users WHERE username='$escapedUsername'");
		$row = mysqli_fetch_array($row);
		if($row['username'] == $username and password_verify($password, $row['password'])){
			return True;
		}
		return False;
	}

	public static function escapeInput($input){
		switch (gettype($input)) {
			case 'string':
				return addslashes($input);
				break;

			case 'array':
				$returnArray = array();
				foreach($input as $key => $item){
					$returnArray[$key] = addslashes($item);
				}
				return $returnArray;
				break;

			default:
				return False;
				break;
		}
	}
	public function updateValue($valueName, $value){
		$this->$valueName = $value;
		mysqli_query($con, "UPDATE settings SET value='$value' WHERE name='$valueName'");
	}
	public function getQuestions(){
		return Question::getAll();
	}
}
?>