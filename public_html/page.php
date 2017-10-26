<?php
require('../core/init.php');


try {

$db = \MyApp\Db::getInstance();
$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'page', 'footer']);

$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');
 
$flash = null;
if ($flash = \MyApp\FlashMessage::render()) {
  $view->flash = $flash;
}


$userdata=array();

if (isset($_GET['user'])) {
	$user = new \MyApp\User($_GET['user']);
	if ($userdata = $user->getData()) {
		$view->user = $userdata;
		$view->title = $userdata['username'];
	}else {
		\MyApp\Redirect::to('index.php');
		die();
	}
} elseif (\MyApp\Session::exists(\MyApp\Config::get('session/session_name'))) {
	$user = new \MyApp\User();
	if ($userdata = $user->getData()) {
		$view->user = $userdata;
		$view->title = $userdata['username'];
	}
} else {
	\MyApp\Redirect::to('index.php');
	die();
}


$view->render();


}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}