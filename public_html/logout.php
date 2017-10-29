<?php
require('../core/init.php');

if (!\MyApp\User::isLogin()) {
	\MyApp\Redirect::to('index.php');
	die();
}

$user = new \MyApp\User();
$user->logout();

\MyApp\Redirect::to('index.php');
die();