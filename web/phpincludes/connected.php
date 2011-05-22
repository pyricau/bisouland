<?php
//Ce qu'on affiche si on est connecté
if ($_SESSION['logged'] == true)
{

	$sql_info = mysql_query("SELECT alerte, espion FROM membres WHERE id='".$id."'");
	$donnees_info = mysql_fetch_assoc($sql_info);
	$alerte=$donnees_info['alerte'];
	$espion=$donnees_info['espion'];
	
	if (isset($_POST['infos']))
	{
		if (isset($_POST['alerte']))
		{
			$al2=1;
		}
		else
		{
			$al2=0;
		}
		if ($alerte!=$al2)
		{
			$alerte=$al2;
			mysql_query('UPDATE membres SET alerte=' . $alerte . " WHERE id=".$id."");
		}
		if (isset($_POST['espion']))
		{
			$esp=1;
		}
		else
		{
			$esp=0;
		}
		if ($espion!=$esp)
		{
			$espion=$esp;
			mysql_query('UPDATE membres SET espion=' . $espion . " WHERE id=".$id."");
		}
	}

?>
<br />
<form method="post" action="connected.html">

	<label>
		<input type="checkbox" <?php if ($alerte==1){echo 'checked="checked"';} ?> name="alerte" />
		Je souhaite être averti par mail de chaque nouveau message.
	</label><br />
	<label>
		<input type="checkbox" <?php if ($espion==1){echo 'checked="checked"';} ?> name="espion" />
		Je souhaite enregistrer dans des messages les informations que j'obtiens sur des joueurs.
	</label><br />		
	<input type="submit" name="infos" value="Envoyer" />
</form>
<br />
<a href="changepass.html" title="Changer de mot de passe.">Je désire changer de mot de passe.</a><br />
<br />
Si tu en as ras le bol des bisous, tu peux supprimer ton compte !!<br />
<form method="post" action="accueil.html" id="supprime">
	<input type="button" value="Supprimer" onclick="if (confirm('Malheureux, es tu bien sûr de vouloir supprimer ton compte ?')) { document.forms.supprime.submit(); } else  { exit; }" />
	<input type="hidden" name="suppr">
</form>
<?php
}
else
{
	echo 'Erreur : Vous vous croyez ou la ??';
	echo '<br />Veuillez vous connecter.';
}


?>