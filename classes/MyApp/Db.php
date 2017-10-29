<?php
namespace MyApp;

/**
 *Klasa odpowiadajaca za tworzenie, utrzymyanie polaczenia z baza danych oraz realizacje zapytan;
 */
class DB{

/**
 * Przechowuje instancje klasy DB
 * @var $_instance
 */
	private static $_instance = null;

/**
 * Przechowuje obiekt PDO (polaczenie z mysql)
 * @var _pdo
 */
	private $_pdo;
	private $_stmt;

/**
 * Przechowuje wyniki zapytania mysql
 * @var [type]
 */
	private $_results;
	private $_errors;
			

/**
 * Tworzy i zapisuje obiekt PDO
 */
	private function __construct(){
		try {
			$this->_pdo = new \PDO('mysql:host='. Config::get('database/host').';dbname='.Config::get('database/dbname'), Config::get('database/username'), Config::get('database/password'));
			$this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die('Error-Connection database!');
		}
	}

/**
 * (Singleton) Sprawdza czy istnieje instancja db i jezeli nie istnieje to tworzy nowa
 * @return     DB  Instacja kalasy DB.
 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			self::$_instance = new DB;
		}
		return self::$_instance;
	}

/**
 * Czysci instancje po otrzymaniu wynikow z wyslanego zapytania
 */
	private function cleanDb(){
		$this->_stmt = null;
		$this->_results = null;
		$this->_errors = null;
	}
	
/**
 * 
 * @param  string $sql        	Zapytanie sql w formie np. "SELECT * FROM username WHERE id = ?"
 * @param  array  $bindValues 	Tablica wartosci ktore maja zostac zbindowane
 * @return integer             	Zwraca liczbe rekordow bazy, ktore zareagowaly na zapytanie
 */
	private function query($sql, $bindValues){
		$this->cleanDb();

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
			return 0;
		}


	}

/**
 * Dodaje dodatkowe parametry na koniec zapytania sql (uzywana w select)
 * 
 * @param array $options parametry
 * 
 * @return string            	Zwraca sql z dodanymi parametrami
 */
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

/**
 * Tworzy sql zapytania SELECT i wysyla je do funkcji query wraz z lista parametrow.
 * Przyklad:
 * 
 * <pre>
 * \MyApp\Db::getInstance()->select('users', ['id', =, 2], ['orderby'=>'id','sort' => 'ASC','limit'=>5]));
 * </pre>
 *
 * @param      string  $from     nazwa tablicy
 * @param      array   $where    warunek wyszukiwania w bazie
 * @param      array   $options  dodatkowe parametry na koncu sql
 *
 * @return     Zwraca tablice z pobranymi danymi lub null
 */
	public function select($from, $where = null, $options = null) {
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
			$this->_results = $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $this->_results;
		}
		return null;

	}

/**
 * Tworzy sql zapytania INSERT i wysyla je do funkcji query wraz z lista parametrow.
 * Przyklad:
 * 
 * <pre>
 * \MyApp\Db::getInstance()->insert('users', ['username'=> 'marek5','password'=> 'marek4haslo','email'=> 'marek4@gmail.com']);
 * </pre>
 * @param  string $into   nazwa tabeli
 * @param  array $params  tablica kolumna => wartosc
 * @return integer        Zwraca liczbe rekordow bazy, ktore zareagowaly na zapytanie 
 */
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
		
		if($this->_results = $this->query($sql, $bindParams)){
			return $this->_results;
		}
		return 0;
	}

/**
 * Tworzy sql zapytania UPADTE i wysyla je do funkcji query wraz z lista parametrow.
 * Przykad:
 * 
 * <pre>
 * \MyApp\Db::getInstance()->update('users', ['id','=', 1], ['username'=> 'jasiu3','password'=> 'kasztann','email'=> 'jasiu@gmail.com']);
 * </pre>
 * @param  string $what   nazwa tabeli
 * @param  array  $where  warunek wyszukiwana w mysql
 * @param  array  $params parametry ktore maja zostac zmienione
 * @return integer         Zwraca liczbe rekordow bazy, ktore zareagowaly na zapytanie 
 */
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
		
		if($this->_results = $this->query($sql, $bindParams)){
			return $this->_results;
		}

		return 0;
	}

/**
 * Tworzy sql zapytania DELETE i wysyla je do funkcji query wraz z lista parametrow.
 * Przykad:
 * 
 * <pre>
 * \MyApp\Db::getInstance()->delete('users', ['id','=', 2]);
 * </pre>
 * @param  string  $from  nazwa tabeli
 * @param  array  $where warunek wyszukiwania mysql
 * @return integer         Zwraca liczbe rekordow bazy, ktore zareagowaly na zapytanie 
 */
	public function delete($from, $where){
		$sql = 'DELETE FROM '. $from;
		$bindValue = array();

		$sql .= ' WHERE '. $where[0].$where[1]. '?';
		$bindValue[] = $where[2];
		
		if($this->_results =$this->query($sql, $bindValue)){
			return $this->_results;
		}
		return 0;
	}

/**
 * Zwraca wynik wynik zapytania do bazy danych
 */
	public function getResults(){
		return $this->_results;
	}







}