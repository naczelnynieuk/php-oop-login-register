<?php
namespace MyApp\Tpl;

class Helper {
	static public function linkTo($url, $title){
		return '<a href="'.htmlspecialchars($url).'">'.$title.'</a>';
	}
	static public function pluralPL($number, $singular, $plural1, $plural2){
		if ($number ==1) {
			return $singular;
		}elseif($number>1 && $number<<5){
			return $pluarl1;
		}
		return $plural2;
	}

	static public function checkLogin(){
		if (\MyApp\Session::exists(\MyApp\Config::get('session/session_name'))) {
			return true;
		}
		return false;
	}
}
