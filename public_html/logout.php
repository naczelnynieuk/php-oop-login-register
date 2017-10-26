<?php
require('../core/init.php');
$user = new \MyApp\User();
$user->logout();

\MyApp\Redirect::to('index.php');
die();