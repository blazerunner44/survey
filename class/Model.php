<?php 
require_once __DIR__ . '/BuilderContainer.php';
abstract class Model{
	public $pk;

	public static function all(){
		$columns = self::getArgsList();
		array_unshift($columns, self::getPkField());
		$builder = new BuilderContainer(get_called_class(), self::getTableName());
		return $builder->setColumns($columns);
	}

	public static function get($query){
		$con = self::getConnection();
		$list = array();

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
		$list = array();
		$r = new ReflectionMethod(get_called_class(), $method);
		$params = $r->getParameters();
		foreach ($params as $param) {
		    array_push($list, $param->getName());
		}
		return $list;
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