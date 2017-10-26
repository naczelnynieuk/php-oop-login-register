<?php 

function pS($data){
	print_r($data);
}
function pA($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function vS($data){
	var_dump($data);
}
function vA($data){
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}

function escape($string){
 return htmlentities($string, ENT_QUOTES, 'UTF-8');
}