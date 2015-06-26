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
}
?>