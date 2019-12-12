<?php 

abstract class Model{
	private function getConnection(){
		return mysqli_connect(
			'localhost', //Database server
			'admin_survey', //Database user
			'survey', //Database password
			'admin_survey' //Database name
			);
	}

	public static function getAll(){
		$con = self::getConnection();

		$list = array();
		
		$query = "SELECT " . self::getArgsList() . " FROM " . self::getTableName();
		$result = $con->query($query);
		if(!$result){
			throw new Exception("Error executing query " . $query, 1);
		}

		while($row = $result->fetch_array(MYSQLI_NUM)){
			array_push($list, new static(...$row));
		}

		return $list;
	}

	private static function getArgsList($method = '__construct'){
		$string = '';
		$r = new ReflectionMethod(get_called_class(), $method);
		$params = $r->getParameters();
		foreach ($params as $param) {
		    //$param is an instance of ReflectionParameter
		    $string .= $param->getName() . ',';
		}
		$string = rtrim($string, ',');
		return $string;
	}

	private static function getTableName(){
		if(!empty(static::tableName)){
			return static::tableName;
		}
		return strtolower(static::class);
	}
}