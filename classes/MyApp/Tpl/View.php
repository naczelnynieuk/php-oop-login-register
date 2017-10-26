<?php
namespace MyApp\Tpl;

class View{
	private $_template;
	private $_files= array();
	private $_templatePath;
	private $_data = array();
	public function __construct($template, $files){
		$this->_templatePath = $template;
		$this->_files = $this->checkFiles($files);
	}


	public function checkFiles($files){
		$correctFiles = array();
		foreach ($files as $file) {
			if (!file_exists($this->_templatePath.$file.'.php')) {
				throw new \Exception('Określony plik ' . $file. '.php nie istnieje!');
			}
			$correctFiles[]= $file;
		}

		return $correctFiles;
	}

	public function getTemplate(){
		return $this->_templatePath;
	}

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	public function __get($name){
		return $this->_data;
	}

	public function render(){
		extract($this->_data);
		foreach ($this->_files as $file) {
			require($this->_templatePath. $file. '.php');
		}
	}
}

