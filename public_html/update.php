<?php
require('../core/init.php');


try {

$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'update', 'footer']);

$view->title = \MyApp\Config::get('system/default_title').' - Update';
$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');


$flash = null;
if ($flash = \MyApp\FlashMessage::render()) {
  $view->flash = $flash;
}



$userdata=array();

$user = new \MyApp\User();
if($user->isExists()){
	$userdata = $user->getData();
	$view->user = $userdata;
}else{
	\MyApp\FlashMessage::add('Zaloguj się aby edytować konto!');
	\MyApp\Redirect::to('index.php');
	die();
}


if (isset($_POST['update_password'])) {

	if (\MyApp\Token::check($_POST['token'])) {
		$form_data = array();
		$form_data[] = new \MyApp\Validation('Aktualne hasło',$_POST['current_password'], [
			'maxlength'=>32,
			'minlength'=>3,
			'matchpassword'=>$userdata
			]);

		$form_data[] = new \MyApp\Validation('Nowe hasło',$_POST['password'], [
			'maxlength'=>32,
			'minlength'=>3
			]);

		$form_data[] = new \MyApp\Validation('Ponownie nowe hasło',$_POST['password_reply'], [
			'maxlength'=>32,
			'minlength'=>3,
			'equals'=> $form_data[1]
			]);

		foreach ($form_data as $data) {
			$data->validate();
		}

		if(!($view->errors = $form_data[0]->getErrors())){
			// Poprawnie zalogowany
			$user->update('users', ['id','=', $userdata['id']], [
	  		'password'=> password_hash(trim($_POST['password']),PASSWORD_DEFAULT)
	  	]);
			\MyApp\FlashMessage::add('Poprawnie zaktualizowano dane!');
			\MyApp\Redirect::to('index.php');
			die();
		}
	}
}

if (isset($_POST['update_basic'])) {
	if (\MyApp\Token::check($_POST['token'])) {
		$form_data = array();

		$form_data[] = new \MyApp\Validation('Email', $_POST['email'], [
			'maxlength'=>32,
			'minlength'=>3,
			'notExistDb'=>'users/email'
		]);


		foreach ($form_data as $data) {
			$data->validate();
		}

		if(!($view->errors = $form_data[0]->getErrors())){
			// Poprawnie zalogowany
			$user->update('users', ['id','=', $userdata['id']], [
	  			'email'=> trim($_POST['email'])
	  		]);
	  		
			\MyApp\FlashMessage::add('Poprawnie zaktualizowano dane!');
			\MyApp\Redirect::to('index.php');
			die();
		}

	}
}











$view->render();


}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}


