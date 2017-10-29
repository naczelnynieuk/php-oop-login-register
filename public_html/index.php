<?php
require('../core/init.php');


try {

//system szablonow
$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'index', 'footer']);


$view->title = \MyApp\Config::get('system/default_title');
$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');

//wiadomosci flash
$flash = null;
if ($flash = \MyApp\FlashMessage::render()) {
  $view->flash = $flash;
}

//dane aktywnego uzytkownika, jezeli istnieje
$userdata=array();
$user = new \MyApp\User();

if($user->isExists()){
  $view->user = $user->getData();
}

$view->render();


}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}
