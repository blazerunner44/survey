<?php
require_once __DIR__ . '/../vendor/autoload.php';
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
class BuilderContainer{
	private $builder;
	private $query;
	private $modelObject;

	public function __construct($modelObject, $tableName){
		$this->builder = new GenericBuilder();
		$this->query = $this->builder->select()->setTable($tableName);
		$this->modelObject = $modelObject;
	}

	public function get(){
		$query = $this->builder->write($this->query);
		$values = $this->builder->getValues();

		foreach($values as $key => $value){
		    $query = str_replace($key, addslashes($value), $query);
		}

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
}