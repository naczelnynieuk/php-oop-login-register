<?php

namespace MyApp\Tpl;

class Engine {
	private $_templateDir;

	public function __construct($templateDir){
		$this->setTemplateDir($templateDir);
	}


	public function setTemplateDir($dir){
		if (!is_dir($dir)) {
			throw new \Exception('Podany katalog szablonów '.$dir. ' jest niedostępny.');
		}
		if(strlen($dir) > 0 && $dir[strlen($dir) - 1] != '/'){
			$dir .= '/';
		}
		$this->_templateDir = $dir;
	}

	public function getTemplateDir(){
		return $this->_templateDir;
	}

	public function createView($files){
		return new View($this->_templateDir, $files);
	}
}