<?php

namespace Core;

use PDO;

Class DatabaseConnection {
	private static $connection=null;
	private static $instance=null;

	public static function getInstance(){
		if(!isset(self::$instance)){
              self::$instance = new DatabaseConnection();
    	}
    	return self::$instance; 
	}

	public function connect(){
		$servername = env("DB_HOST");
		$db=env("DB_NAME");
		$username=env("DB_USERNAME");
		$password=env("DB_PASSWORD");

		try{
			self::$connection = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
			self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  		} catch(PDOException $e) {
  			throw \Exception("Failed to connect to MySQL:". $e->getMessage());
		}
	}

	public function getConnection(){
		if(self::$connection==null){
			$this->connect();
		}
		return self::$connection;
	}

	public function disconnect(){
		self::$connection=null;
	}
}