<?php
namespace MyApp;

class DB{
	private static $_instance = null;
	private $_pdo,
			$_stmt,
			$_results,
			$_errors;
			

	private function __construct(){
		try {
			$this->_pdo = new \PDO('mysql:host='. Config::get('database/host').';dbname='.Config::get('database/dbname'), Config::get('database/username'), Config::get('database/password'));
			$this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die('Error-Connection database!');
		}
	}

	public static function getInstance(){
		if (!isset(self::$_instance)) {
			self::$_instance = new DB;
		}
		return self::$_instance;
	}

	private function cleanDb(){
		$this->_stmt = null;
		$this->_results = null;
		$this->_errors = null;
	}
	//sql - SELECT * FROM Users WHERE Username = ?
	

	private function query($sql, $bindValues){
		try {
		$bindValuesNumber = count ($bindValues);
		$this->_stmt = $this->_pdo->prepare($sql);
		$x =1;
		foreach ($bindValues as $value) {
			$this->_stmt->bindValue($x, $value, \PDO::PARAM_STR);
			$x++;
		}
			$this->_stmt->execute();


			return $this->_stmt->rowCount();
		} catch (\PDOException $e) {
			$this->_errors[] = 'Blad podczas wykonywania zapytania mysql';
			return false;
		}


	}


	private function addOptions($options){
		$optionsql ='';

		foreach ($options as $key => $value) {
			switch ($key) {
				case 'orderby':
					$optionsql.= ' ORDER BY '. $value;
					break;
				case 'sort':
					$optionsql.= ' '.$value;
				break;
				case 'limit': 
					$optionsql.= ' LIMIT '.$value;
				break;
				default:
					break;
			}
		}
		return $optionsql;
	}



	public function select($from, $where = null, $options = null) {
		$results = null;
		$sql = 'SELECT * FROM '. $from;
		$bindvalue = array();

		if ($where) {
			$sql .= ' WHERE '. $where[0].$where[1]. '?';
			$bindvalue[] = $where[2];
		}
		if ($options) {
			$sql .= $this->addOptions($options);
		}

		if($this->query($sql, $bindvalue)) {
			$results = $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
			$this->cleanDb();
			return $results;
		}
		$this->cleanDb();

	}


	public function insert($into, $params){
		$bindParams = array();
		$paramsNumber = count($params);
		$sql = 'INSERT INTO '. $into . '(';

		$x=1;
		foreach ($params as $key => $value) {
			if ($x == $paramsNumber) {
				$sql.= $key;
				$bindParams[]=$value;
				break;
			}
			$sql.= $key.',';
			$bindParams[]=$value;
			$x++;
		}
		$sql .=') VALUES (';

		for ($i=0; $i <$paramsNumber ; $i++) { 
			if ($i == $paramsNumber-1) {
				$sql .= '?)';
				break;
			}
			$sql .= '?,';
		}
		
		if($result = $this->query($sql, $bindParams)){
			$this->cleanDb();
			return $result;
		}
		$this->cleanDb();
		return null;
	}



	public function update($what, $where, $params){
		$bindParams = array();
		$paramsNumber = count($params);
		$sql = 'UPDATE '. $what . ' SET ';

		$x =1;
		foreach ($params as $key => $value) {
			if ($x == $paramsNumber) {
				$sql.= $key.'=? ';
				$bindParams[]=$value;
				break;
			}
			$sql.= $key.'=?,';
			$bindParams[]=$value;
			$x++;
		}
		$sql .=' WHERE '.$where[0].$where[1].'?';
		$bindParams[]= $where[2];
		
		if($result = $this->query($sql, $bindParams)){
			$this->cleanDb();
			return $result;
		}

		$this->cleanDb();
		return null;
	}


	public function delete($from, $where){
		$sql = 'DELETE FROM '. $from;
		$bindValue = array();

		$sql .= ' WHERE '. $where[0].$where[1]. '?';
		$bindValue[] = $where[2];
		
		if($result =$this->query($sql, $bindValue)){
			$this->cleanDb();
			return $result;
		}
		$this->cleanDb();
		return null;
	}

	public function getResults(){
		return $this->_results;
	}







}