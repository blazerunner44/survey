<?php
require_once __DIR__ . '/../vendor/autoload.php';
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
class BuilderContainer{
	private $builder;
	private $query;
	private $modelObject;
	private $tableName;

	public function __construct($modelObject, $tableName){
		$this->builder = new GenericBuilder();
		$this->tableName = $tableName;
		$this->modelObject = $modelObject;
	}

	public function select(){
		$this->query = $this->builder->select()->setTable($this->tableName);
		return $this;
	}

	public function insert(){
		$this->query = $this->builder->insert()->setTable($this->tableName);
		return $this;
	}

	public function update(){
		$this->query = $this->builder->update()->setTable($this->tableName);
		return $this;
	}

	public function delete(){
		$this->query = $this->builder->delete()->setTable($this->tableName);
		return $this;
	}

	public function setValues($values){
		$this->query->setValues($values);
		return $this;
	}

	public function getQueryString(){
		$query = $this->builder->write($this->query);
		$values = $this->builder->getValues();

		$query = str_replace($this->tableName . '.', '', $query);

		foreach($values as $key => $value){
			switch (gettype($value)) {
				case 'string':
					$value = '"' . addslashes($value) . '"';
					break;
				case 'NULL':
					$value = '';
					break;
				case 'integer':
					break;
				default:
					$value = addslashes($value);
					break;
			}
		    $query = str_replace($key, $value, $query);
		}

		return $query;
	}

	public function get(){
		$query = $this->getQueryString();
		return $this->modelObject::get($query);
	}

	public function setColumns($columns){
		$this->query->setColumns($columns);
		return $this;
	}

	public function where(){
		$this->query->where();
		return $this;
	}

	public function equals($column, $value){
		$this->query->where()->equals($column, $value)->end();
		return $this;
	}

	public function end(){
		$this->query->end();
		return $this;
	}

	public function orderBy($column, $direction){
		if($direction == 'desc'){
			$this->query->orderBy($column, OrderBy::DESC);
		}
		if($direction == 'asc'){
			$this->query->orderBy($column, OrderBy::ASC);
		}
		
		return $this;
	}
}