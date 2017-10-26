<?php use MyApp\Tpl\Helper as Helper;?>

<?php if (isset($flash)) {?>
	<h1>Kominkaty:</h1>
	<ul>
	<?php foreach ($flash as $value){ ?>
		<li><?= $value ?></li>
	<?php } ?>
	</ul>
	<br><br><br><br>
<?php } ?>


<h2>Użytkownicy</h2>
<?php foreach ($users as $key => $value) { ?>

	<h2><?php echo escape($value['username']); ?></h2>
	<ul>

	<li>username: <?php echo escape($value['username']); ?></li>
	<li>email: <?php echo escape($value['email']); ?></li>
	<li><?php echo Helper::linkTo('?usun='. escape($value['id']), 'usun') ?></li>

	</ul>
<?php
}

?>