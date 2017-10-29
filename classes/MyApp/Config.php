<?php 
namespace MyApp;

class Config{
	private static $array = array();

	public static function downloadConfig($file){
		self::$array = parse_ini_file($file, true);
	}


	public static function get($from){
		$path = array();
		if (strpos($from, '/')) {
			$path = explode("/", $from);
			if (isset(self::$array[$path[0]][$path[1]])) {
				return self::$array[$path[0]][$path[1]];
			}
			return false;
		} else {
			if (isset(self::$array[$from])) {
				return self::$array[$from];
			}
			return false;
		}
		
	}

}