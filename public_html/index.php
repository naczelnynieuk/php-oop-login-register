<?php
require('../core/init.php');


try {

$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'index', 'footer']);

$view->title = \MyApp\Config::get('system/default_title');
$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');
$view->name =  'Zaloguj się lub zarejestruj';

$flash = null;
if ($flash = \MyApp\FlashMessage::render()) {
  $view->flash = $flash;
}

$userdata=array();

$user = new \MyApp\User();
if($userdata = $user->getData()){
  $view->user = $userdata;
}

$view->render();


}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}




/*


pA( \MyApp\Db::getInstance()->select('users', null, [
  	'orderby'=>'id',
  	'sort' => 'ASC',
  	'limit'=>5
  	]));

 */


/*
echo \MyApp\Db::getInstance()->insert('users', [
 	'username'=> 'marek5',
 	'password'=> 'marek4haslo',
 	'email'=> 'marek4@gmail.com'
 	]);
*/
/*
echo \MyApp\Db::getInstance()->update('users', ['id','=', 1], [
  	'username'=> 'jasiu3',
  	'password'=> 'kasztann',
  	'email'=> 'jasiu@gmail.com'
  	]);
 */

// echo \MyApp\Db::getInstance()->delete('users', ['password','=','haslo']);