
<h2>Zaloguj</h2>
<form action="login.php" method="post">
	<label for="">username <input type="text" name="username" value="<?php echo escape($form['username']); ?>"></label><br>
	<label for="">password <input type="text" name="password"></label><br>
	<label>remember <input type="checkbox" name="remember" checked="on"></label><br>
	 <input type="hidden" name="token" value="<?php echo \MyApp\Token::generate();?>">
	<input type="submit" value="zaloguj" name="login">
</form>

<?php 
if (isset($errors)) {
	pA($errors);
} ?>