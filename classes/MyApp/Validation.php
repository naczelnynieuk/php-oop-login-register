<?php
namespace MyApp;

class Validation {
	private $_data,
			$_name,
			$_result,
			$_options = array();

	private static 	$_errors = array(),
					$_instance;

	public function __construct($name, $data, $options){
		$this->_data = trim($data);
		$this->_name = $name;
		$this->_options = $options;

		if (!isset(self::$_instance)) {
			self::$_instance = Db::getInstance();
		}
	}

	public function validate(){
		$path = array();
		foreach ($this->_options as $key => $value) {
			$this->_result = null;
			switch ($key) {
				case 'required':
					$this->required($value);
					break;
				case 'maxlength':
					$this->maxLength($value);
					break;
				case 'minlength':
					$this->minLength($value);
					break;
				case 'existDb':
					$this->existDb($value);
					break;
				case 'notExistDb':
					$this->notExistDb($value);
					break;
				case 'equals':
					$this->equals($value);
					break;
				case 'matchpassword':
					$this->matchPassword($value);
					break;
				default:
					break;
			}
		}
	}

	public function getErrors(){
		return self::$_errors;
	}
	public function getResult(){
		return $this->_result;
	}
	public function getData(){
		return $this->_data;
	}
	public function getName(){
		return $this->_name;
	}


	private function required($value){
		if ($value) {
			if ($this->_data == ''){
			self::$_errors[]= 'Pole "'.$this->_name. '" nie może być puste!';
			}
		}
	}

	private function equals($value){
		if (isset($value)) {
			if (!($this->_data == $value->getData())) {
				self::$_errors[]= 'Pole "'.$this->_name. '" oraz "' . $value->getName().'" nie jest takie same!';
			}		
		}
	}
	private function maxLength($value){
		if (strlen($this->_data) > $value) {
			self::$_errors[]= 'Pole "'.$this->_name. '" ma za dużo znaków (max '. $value . ' zn.)';
		}
	}

	private function minLength($value){
		if (strlen($this->_data) < $value) {
			self::$_errors[]= 'Pole "'.$this->_name. '" ma za mało znaków (min '. $value . ' zn.)';
		}
	}

	private function matchPassword($value){
		if(is_object($value)){
			if (!password_verify($this->_data, $value->getResult()['password'])) {
				self::$_errors[] = 'Pole '.$this->_name.' jest niepoprawne.';
			}
		}
		if(is_array($value)){
			if (!password_verify($this->_data, $value['password'])) {
				self::$_errors[] = 'Pole '.$this->_name.' jest niepoprawne.';
			}
		}
	}
	private function existDb($value){
		$path = explode('/', $value);
		$where = [$path[1], '=', $this->_data];

		$this->_result = self::$_instance->select($path[0], $where)[0];

		if (!$this->getResult()) {
			self::$_errors[]= 'Wartość pola "'. $this->_name. '" nie istnieje w bazie!' ;
		}
	}

	private function notExistDb($value){
		$path = explode('/', $value);
		$where = [$path[1], '=', $this->_data];

		$this->_result = self::$_instance->select($path[0], $where);
		if ($this->getResult()) {
			self::$_errors[]= 'Wartość pola "'. $this->_name. '" istnieje w bazie!' ;
		}
	}
}