<?php
namespace MyApp;

class Redirect{
	public static function to($url){
		header("Location: {$url}");
		die();
	}
}