<?php use MyApp\Tpl\Helper as Helper;?>
<body>

<?php if (!Helper::checkLogin()): ?>
	<h2>Witaj na stronie głównej!</h2>
	<p>Wybierz jedną z poniższych akcji:</p>
	<?php echo Helper::linkTo('login.php', 'logowanie') ?> <br>
	<?php echo Helper::linkTo('register.php', 'rejestracja') ?><br><br><br>



<?php if (isset($flash)) {?>
	<h2>Kominkaty:</h2>
	<ul>
	<?php foreach ($flash as $value){ ?>
		<li><?= escape($value); ?></li>
	<?php } ?>
	</ul>
<?php } ?>

	

<?php else: ?>

<h2>Witaj <?php echo escape($user['username']); ?></h2>

<?php echo Helper::linkTo('page.php', 'Profil') ?><br>
<?php echo Helper::linkTo('update.php', 'Aktualizuj dane') ?><br>
<?php if ($user['permission'] == 1): ?>
	<?php echo Helper::linkTo('admin.php', 'Panel Administratora') ?><br>
<?php endif ?>
<?php echo Helper::linkTo('logout.php', 'Wyloguj') ?><br>
<br><br><br>

<?php if (isset($flash)) {?>
	<h2>Kominkaty:</h2>
	<ul>
	<?php foreach ($flash as $value){ ?>
		<li><?= escape($value); ?></li>
	<?php } ?>
	</ul>
<?php } ?>


<?php endif ?>
