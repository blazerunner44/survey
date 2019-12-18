<?php 

abstract class Model{
	public $pk;

	public static function getAll(){
		$con = self::getConnection();

		$list = array();
		
		$query = "SELECT " . self::getPkField() . ',' . self::getArgsList() . " FROM " . self::getTableName();
		$result = $con->query($query);
		if(!$result){
			throw new Exception("Error executing query " . $query, 1);
		}

		while($row = $result->fetch_array(MYSQLI_NUM)){
			$obj = new static(...array_slice($row, 1, count($row)-1));
			$obj->pk = $row[0];
			array_push($list, $obj);
		}
		return $list;
	}

	public static function getByColumnEqual($colName, $colValue){
		$con = self::getConnection();

		$list = array();
		
		$query = "SELECT " . self::getPkField() . ',' . self::getArgsList() . " FROM " . self::getTableName() . " WHERE " . $con->escape_string($colName) . "=" . $con->escape_string($colValue);
		$result = $con->query($query);
		if(!$result){
			throw new Exception("Error executing query " . $query, 1);
		}

		while($row = $result->fetch_array(MYSQLI_NUM)){
			$obj = new static(...array_slice($row, 1, count($row)-1));
			$obj->pk = $row[0];
			array_push($list, $obj);
		}
		return $list;
	}

	public static function getByPkEqual($colValue){
		return self::getByColumnEqual(self::getPkField(), $colValue);
	}

	private function getConnection(){
		return mysqli_connect(
			'localhost', //Database server
			'admin_survey', //Database user
			'survey', //Database password
			'admin_survey' //Database name
			);
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
		if(defined(get_called_class() . '::tableName')){
			return static::tableName;
		}
		return strtolower(static::class);
	}

	private static function getPkField(){
		if(defined(get_called_class() . '::pkField')){
			return static::pkField;
		}
		return 'id';
	}
}