<?php
namespace MyApp;

class User{

	private $_instance,
			$_userData,
			$_result,
			$_sessionName;


	public function __construct($user = null){

		$this->_instance = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		$this->_result = null;

		if (!$user && isset($_SESSION[$this->_sessionName])) {
			$user = $_SESSION[$this->_sessionName];
		}
		trim($user);
		$this->getUser($user);

	}

	private function getUser($user=null){
		if (is_string($user)) {
			$this->_userData = $this->_instance->select('users',['username','=', $user], [
					'limit'=>1
				])[0];
			$this->setPermission();
			$this->_result = true;

		}
		if (is_int($user)) {
			$this->_userData = $this->_instance->select('users',['id','=', $user], [
					'limit'=>1
				])[0];
			$this->setPermission();
			$this->_result = true;
		}
	}

	public function getData(){
		return $this->_userData;
	}
	public function getResult(){
		return $this->_result;
	}

	public function update($what, $where, $params){
		$this->_result = null;
		$this->_result = $this->_instance->update($what, $where, $params);
		return $this->_result;
	}

	public function register($data){
		$this->_result = null;
		$this->_result = $this->_instance->insert('users', [
			'username' => $data['username'],
			'password' => \password_hash($data['password'],PASSWORD_DEFAULT),
			'email'	   => $data['email']
			]);
		return $this->getResult();

	}


	public static function login($data, $remember = false) {

		$user = new User($data);

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

	public function logout(){
		$this->_result = null;

		$this->_instance->delete('sessions', array('user_id', '=', $this->_userData['id']));
		Session::delete($this->_sessionName);
		\MyApp\Cookie::delete(\MyApp\Config::get('remember/cookie_name'));
		
	}

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
	public function  isAdmin(){
		if ($this->getData()['permission'] ==1 ) {
			return true;
		}

		return false;
	}
}