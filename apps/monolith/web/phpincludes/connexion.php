<h1>Connexion</h1>
<?php
if ($_SESSION['logged'] == false)
{

if (isset($_SESSION['errCon']))
{
	echo '<br />',$_SESSION['errCon'],'<br /><br />';
	unset($_SESSION['errCon']);
}

?>

<form method="post" class="formul" action="redirect.php">
	<label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caractères)</span><br /><input type="text" name="pseudo" tabindex="10" size="15" maxlength="15" value=""/></label><br />
	<label>Mot de passe : <br /><span class="petit">(Entre 5 et 15 caractères)</span><br /><input type="password" name="mdp" tabindex="20" size="15" maxlength="15" value=""/></label><br />
    <label><input type="checkbox" checked="checked" name="auto" />Connexion automatique</label><br />
	<input type="submit" name="connexion" value="Se connecter" /><br /><br />
	<a href="perdu.html">J'ai perdu mon mot de passe</a>
</form>

<?php
}
else
{
	echo 'T\'es dja connected !!';
}
?>