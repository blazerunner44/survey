<?php
class Survey {
	public $name;
    public $email;
	
	public function __construct() {
		require('mysql.php');
		$row = mysqli_query($con, "SELECT value FROM settings WHERE name='name'");
		$row = mysqli_fetch_array($row);
		$this->name = $row['value'];

		$row = mysqli_query($con, "SELECT value FROM settings WHERE name='email'");
		$row = mysqli_fetch_array($row);
		$this->email = $row['value'];
	}

	public static function verifyUser($username, $password){
		require('mysql.php');
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
}
?>