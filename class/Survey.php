<?php
require_once __DIR__ . '/Model.php';
class Survey {
	public $name;
    public $email;
	public $description;

	private $questions = array();
	
	public function __construct() {
		$con = Model::getConnection();
		$sql = mysqli_query($con, "SELECT * FROM settings");
		while($row = mysqli_fetch_assoc($sql)){
			$this->{$row['name']} = $row['value'];
		}
		
	}

	public static function verifyUser($username, $password){
		$con = Model::getConnection();
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
		$con = Model::getConnection();
		$this->$valueName = $value;
		mysqli_query($con, "UPDATE settings SET value='$value' WHERE name='$valueName'");
	}
	public function getQuestions(){
		return Question::all()->orderBy('pos', 'asc')->get();
	}
	public function isInstalled(){
		$con = Model::getConnection();
		$result = mysqli_query($con, "SHOW TABLES LIKE 'settings'");
		if(mysqli_num_rows($result) > 0){
			return True;
		}else{
			return False;
		}
	}
}
?>