<?php
require('../core/init.php');


try {

if (!\MyApp\User::isLogin()) {
  \MyApp\FlashMessage::add('Ooops, nie tym razem złotko:)');
  \MyApp\Redirect::to('index.php');
  die();
}

$user = new \MyApp\User();
if (!$user->isAdmin()) {
  \MyApp\FlashMessage::add('Nie jesteś administratorem!');
  \MyApp\Redirect::to('index.php');
  die();
}




$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'admin', 'footer']);

$view->title = \MyApp\Config::get('system/default_title'). ' - Admin';
$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');

$flash = null;
if ($flash = \MyApp\FlashMessage::render()) {
  $view->flash = $flash;
}


if (isset($_GET['usun'])) {
  $result = \MyApp\Db::getInstance()->delete('users', ['id', '=', trim($_GET['usun']) ]);

  if (!$result) {
    \MyApp\FlashMessage::add('Błąd podczas usuwania użytkownika');
    \MyApp\Redirect::to('admin.php');
    die();
  }

   \MyApp\FlashMessage::add('Pomyślnie usunięto użytkownika');
    \MyApp\Redirect::to('admin.php');
    die();
}



$userdata=array();

if($user->isExists()){
  $userdata = $user->getData();
  $view->user = $user->getData();
}

$users= \MyApp\Db::getInstance()->select('users');
if ($users) {
  $view ->users = $users;
}

$view->render();


}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}