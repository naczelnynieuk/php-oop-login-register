<?php
namespace MyApp;
/**
 * Klasa odpowiadajaca za operacje na uzytkowniku
 */
class User{

/**
 * Instancja klasy Db
 * @var Db
 */
	private $_instance;

/**
 * Dane uzytkownika
 * @var array
 */
	private	$_userData;

/**
 * Przechowuje informacje o istnieniu uzytkownika
 * @var boolean
 */
	private	$_exists;

/**
 * Przechowuje informacje o rezultacie zapytania
 */
	private	$_result;

/**
 * Pobiera obiekt klasy Db, oraz tworzy instancje uzytkownika
 * 
 * Przyklad
 * 
 * $user = new User(); ->Pobierze dane usera jesli jest zalogowany;
 * 
 * $user = new User(3); $user = new User('janusz'); -> Pobierze uzytkownika o id=3 / username = janusz
 */
	public function __construct($user = null){

		$this->_instance = DB::getInstance();
		$this->_exists = false;

		if (!$user && isset($_SESSION[Config::get('session/session_name')])) {
			$user = $_SESSION[Config::get('session/session_name')];
		}
		trim($user);
		$this->getUser($user);

	}

/**
 * Sprawdza czy uzytkownik istnieje w zaleznosci od id lub username i pobiera jego dane.
 * 
 */
	private function getUser($user=null){

		if (is_int($user)) {
			$this->_userData = $this->_instance->select('users',['id','=', $user], [
					'limit'=>1
				])[0];

			if (!$this->_userData) {
				$this->_exists = false;
			}else {
				$this->setPermission();
				$this->_exists = true;
			}
			return;
		}elseif (is_string($user)) {

			$this->_userData = $this->_instance->select('users',['username','=', $user], [
					'limit'=>1
				])[0];

			if (!$this->_userData) {
				$this->_exists = false;
			}else {
				$this->setPermission();
				$this->_exists = true;
			}
			return;

		}
		$this->_exists = false;
	}

/**
 * Zwraca dane uzytkownika
 * 
 * @return array
 */
	public function getData(){
		return $this->_userData;
	}
/**
 * @return boolean Zwraca informacje czy uzytkownik istnieje.
 */
	public function isExists(){
		return $this->_exists;
	}

	public function getResult(){
		return $this->_result;
	}

/**
 * Wywoluje funkcje update z db i zwraca rezultat
 */
	public function update($what, $where, $params){
		$this->_result = null;

		$this->_result = $this->_instance->update($what, $where, $params);
		return $this->_result;
	}

/**
 * Rejestruje uzytkownia i zwraca rezultat
 * @param  array $data Dane uzytkownika, ktore powinny byc wczesniej przepuszczone przez klase validate
 * @return integer	Zwraca ilosc rekordow, ktore zareagowaly na zapytanie
 */
	public function register($data){
		$this->_result = null;

		$this->_result = $this->_instance->insert('users', [
			'username' => $data['username'],
			'password' => \password_hash($data['password'],PASSWORD_DEFAULT),
			'email'	   => $data['email']
			]);

		return $this->_result;

	}

/**
 * Loguje uzytkownika, tworzy sesje i ciasteczka(remember)
 * @param  integer/string  $user	Id/nazwa uzytkownika
 * @param  boolean $remember 		Czy zapamietac uzytkownika
 * @return boolean
 */
	public static function login($user, $remember = false) {

		$user = new User($user);
		$result = array();

		if ($result = $user->getData()) {
			Session::put(Config::get('session/session_name'), (int)$result['id']);
		}else{
			return false;
		}

		if ($remember) {
		 	$hash = Hash::unique();
		 	$hashCheck = $user->_instance->select('sessions',array('user_id','=', Session::get(Config::get('session/session_name'))));
		 	if (!$hashCheck) {
		 		$user->_instance->insert('sessions', [
					'user_id' => Session::get(Config::get('session/session_name')),
					'hash' => $hash
				]);
		 	}else{
		 		$hash = $hashCheck[0]['hash'];
		 	}
		 	Cookie::put(Config::get('remember/cookie_name'), $hash, Config::get('remember/cookie_expiry'));
		 }
		 return true;
	}

/**
 * Wylogowuje uzyktownika
 */
	public function logout(){
		$this->_result = null;

		$this->_instance->delete('sessions', array('user_id', '=', $this->_userData['id']));
		Session::delete(Config::get('session/session_name'));
		\MyApp\Cookie::delete(\MyApp\Config::get('remember/cookie_name'));
		
	}

/**
 * Sprawdza czy uzytkownik jest zalogowany
 * @return boolean
 */
	public static function isLogin(){
		return (Session::exists(Config::get('session/session_name'))) ? true : false;
	}


	public function setPermission(){
		$this->_result = null;

		$this->_result=$this->_instance->select('permissions', ['user_id','=',$this->_userData['id']]);

		if ($this->_result) {
			$this->_userData['permission'] =  $this->_result[0]['is_admin'];
		}else {
			$this->_userData['permission'] =  0;
		}
	}

/**
 * Sprawdza czy uzytkownik jest adminem
 * @return boolean
 */
	public function  isAdmin(){
		if ($this->getData()['permission'] == 1 ) {
			return true;
		}

		return false;
	}
}