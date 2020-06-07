<?php

namespace Core;

use Core\DatabaseConnection as Db;
use PDO;

class Model{
	private $db=null;
	protected $table=null;
	
	public function __construct(){
		$this->db = db::getInstance();
	}

	public function getTable(){
		if(!is_null($this->table)){
			return $this->table;
		}
		return strtolower(preg_replace('/^(\w+\\\)*/', '', get_class($this)));
	}

	public function setTable($table_name){
		$this->table=$table_name;
	}

	public function insert($query, $params){

		$insertid=-1;
		try{
			$stmt = $this->db->getConnection()->prepare($query);
			$i=1;
			foreach ($params as $param) {
				$stmt->bindValue($i++, $param);
			}
			$stmt->execute();
			$insertid = $this->db->getConnection()->lastInsertId();
		}catch(PDOException $e){
			throw \Exception("Failed to execute query to MySQL:". $e->getMessage());
		}
		return $insertid;
	}

	public function fetch($query, $params){
		$rows=[];
		try{
		$stmt = $this->db->getConnection()->prepare($query);
		$stmt->execute($params);

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			throw \Exception("Failed to execute query to MySQL:". $e->getMessage());
		}
		return $rows;
	}

	public function find(int $id, $select="*"){
		if(is_array($select)){
			$selectColumns = implode(",", $select);
		}else{
			$selectColumns =  $select;
		}
		$where = ['id'=>$id];

		$query= "select ".$selectColumns." from ". $this->getTable();
		if(is_array($where) && count($where)>0){
			$columns=  array_keys($where);
			$query .= " where ". $this->createWhereString($columns);
		}
		return $this->fetch($query, $where);
	}

	public function findWhere($where=[], $select="*"){
		if(is_array($select)){
			$selectColumns = implode(",", $select);
		}else{
			$selectColumns=  $select;
		}

		$query= "select ".$selectColumns." from ". $this->getTable();
		if(is_array($where) && count($where)>0){
			$columns=  array_keys($where);
			$query .= " where ". $this->createWhereString($columns);
		}
		return $this->fetch($query, $where);
	}

	public function findCountWhere($where=[], $select="count(*) as count "){
		if(is_array($select)){
			$selectColumns = implode(",", $select);
		}else{
			$selectColumns=  $select;
		}

		$query= "select ".$selectColumns." from ". $this->getTable();
		if(is_array($where) && count($where)>0){
			$columns=  array_keys($where);
			$query .= " where ". $this->createWhereString($columns);
		}
		$rows = $this->fetch($query, $where);
		return count($rows)>0 ? $rows[0]['count'] : 0;
	}

	private function createWhereString($columns){
		$placeholders=[];
		foreach ($columns as $column) {
			array_push($placeholders, $column."=:".$column);
		}
		return implode(" and ", $placeholders);
	}


	public function create($params){

		$columns = $this->getColumns($params);
		$columnString = $this->getColumnString($columns);
		$placeHolderString = $this->getPlaceHolderString($columns);

		$query = "INSERT INTO ". $this->getTable() ." (".$columnString .") VALUES (".$placeHolderString. ")";
		
		return $this->insertData($query, $params);
	}

	public function insertData($query, $params){
		$insertid=-1;
		try{
		$stmt = $this->db->getConnection()->prepare($query);
		$stmt->execute($params);

		$insertid = $this->db->getConnection()->lastInsertId();
		}catch(PDOException $e){
			throw \Exception("Failed to execute query to MySQL:". $e->getMessage());
		}
		return $insertid;
	}

	private function createValuePlaceHolders($columns){
		$placeholders=[];
		foreach ($columns as $column) {
			array_push($placeholders, ":".$column);
		}
		return $placeholders;
	}

	private function getPlaceHolderString($columns){
		$placeholders = $this->createValuePlaceHolders($columns);
		return implode(",", $placeholders);
	}

	private function getColumns($array){
		return array_keys($array);
	}

	private function getColumnString($columns){
		return implode(",", $columns);
	}
}