<?php 
require_once __DIR__ . '/BuilderContainer.php';
abstract class Model{
	public $pk;

	public static function all(){
		$columns = self::getArgsList();
		array_unshift($columns, self::getPkField());
		$builder = new BuilderContainer(get_called_class(), self::getTableName());
		return $builder->select()->setColumns($columns);
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
		$columns = self::getArgsList();
		array_unshift($columns, self::getPkField());
		$builder = new BuilderContainer(get_called_class(), self::getTableName());
		$builder->select()->setColumns($columns)->equals($colName, $colValue);
		return self::get($builder->getQueryString());
	}

	public static function getByPkEqual($colValue){
		$result = self::getByColumnEqual(self::getPkField(), $colValue);
		if(!empty($result)){
			return $result[0];
		}
		return null;
	}

	public function serialize(){
		$array = array();
		foreach (self::getArgsList() as $column) {
			if(!empty($this->{$column})){
				if(gettype($this->{$column}) == 'array'){
					$array[$column] = json_encode($this->{$column});
				}else{
					$array[$column] = $this->{$column};
				}
			}
		}
		return $array;
	}

	public function save(){
		$builder = new BuilderContainer(get_called_class(), self::getTableName());
		$con = self::getConnection();

		if(!empty($this->pk)){ //This record needs to be updated
			$query = $builder->update()->setValues($this->serialize())->equals(self::getPkField(), $this->pk);
			$query = $query->getQueryString();
			
			if($result = $con->query($query)){
				return true;
			}else{
				throw new Exception("Error executing query: " . $query, 1);
			}
		}else{ //This record needs to be inserted
			$query = $builder->insert()->setValues($this->serialize());
			$query = $query->getQueryString();
			if($result = $con->query($query)){
				$this->{self::getPkField()} = $con->insert_id;
				$this->pk = $con->insert_id;
				return true;
			}else{
				throw new Exception("Error executing query: " . $query, 1);
			}
		}
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