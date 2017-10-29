<?php use MyApp\Tpl\Helper as Helper;?>

<h2>Podstawowe dane</h2>
<form action="update.php" method="post">
	<label>email <input type="text" name="email" value="<?php echo escape($user['email']); ?>" style="width: 200px; padding-left: 10px;"></label>
	<input type="submit" name="update_basic" value="Aktualizuj">
	<input type="hidden" name="token" value="<?php echo $token =\MyApp\Token::generate();?>">
</form>

<h2>Zmien haslo</h2>
<form action="update.php" method="post">
	<label>Aktualne hasło <input type="text" name="current_password"><label><br>
	<label>Nowe hasło <input type="text" name="password"><label><br>
	<label>Powtórz nowe hasło <input type="text" name="password_reply"><label>
	<input type="hidden" name="token" value="<?php echo $token; ?>">
	<input type="submit" name="update_password" value="Aktualizuj">

</form>


<?php 
if (isset($errors)) {
	pA($errors);
} ?>