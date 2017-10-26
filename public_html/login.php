<?php
require('../core/init.php');


try {

if (\MyApp\User::isLogin()) {
	\MyApp\FlashMessage::add('Jesteś już zalogowany!');
	\MyApp\Redirect::to('index.php');
	die();
}

$tpl = new \MyApp\Tpl\Engine('../templates/'. \MyApp\Config::get('system/default_template'));
$view = $tpl->createView(['header', 'login', 'footer']);

$view->title = \MyApp\Config::get('system/default_title').'-login';
$view->lang = \MyApp\Config::get('system/default_lang');
$view->charset = \MyApp\Config::get('system/charset');


//dla zapamiętywania pól formularza, aby nie bylo udefined
$form = array (
		'username'=>''
	);
$view->form = $form;

if (isset($_POST['login'])) {
	if (\MyApp\Token::check($_POST['token'])) {

		$form_data = array();

		$form_data[] = new \MyApp\Validation('Nazwa użytkownika',$_POST['username'], [
			'maxlength'=>32,
			'minlength'=>3,
			'existDb'=>'users/username'
			]);

		$form_data[] = new \MyApp\Validation('Hasło',$_POST['password'], [
			'maxlength'=>32,
			'minlength'=>3,
			'matchpassword'=> $form_data[0]
			]);

		foreach ($form_data as $data) {
			$data->validate();
		}

		if(!($view->errors = $form_data[0]->getErrors())){
			$remember = (isset($_POST['remember']) ? true : false);
			$result = \MyApp\User::login(trim($_POST['username']), $remember );
			if (!$result) {
				\MyApp\FlashMessage::add('Wystąpił błąd podczas operacji logowania!');
				\MyApp\Redirect::to('index.php');
				die();
			}
			
			\MyApp\FlashMessage::add('Poprawnie zalogowano!');
			\MyApp\Redirect::to('index.php');
			die();
		}

		$form = array (
			'username'=>trim($_POST['username'])
		);
		$view->form = $form;
	}

}


$view->render();
}catch (Exception $e) {
	die('Wystąpł błąd: '.$e->getMessage());
}