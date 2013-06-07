<?php

if ($_SESSION['logged'] == true)
{
?>

<h1>Changer de mot de passe</h1>
Il est désormais possible de changer de mot de passe, si l'ancien ne vous convient plus.<br />
<br />
<?php
	if (isset ($_POST['changepswd']))
	{
		if (isset ($_POST['oldpass']) && isset ($_POST['newpass']) && isset ($_POST['newpass2']) && !empty($_POST['oldpass']) && !empty($_POST['newpass']) && !empty($_POST['newpass2']))
		{
			$oldmdp = $_POST['oldpass'];
			$oldmdp = md5($oldmdp);
			//Sélection des informations.
			$sql_info = mysql_query("SELECT mdp FROM membres WHERE id='".$id."'");
			$donnees_info = mysql_fetch_array($sql_info);
			$oldmdp2=$donnees_info['mdp'];
			if ($oldmdp2==$oldmdp)
			{
			        $newpass = $_POST['newpass'];
				$newpass2 = $_POST['newpass2'];
				if ($newpass==$newpass2)
				{
					if (preg_match("!^\w+$!", $newpass))
					{
						$newpass=htmlentities(addslashes($_POST['newpass']), ENT_IGNORE);//Normalement inutile.
						$taille = strlen(trim($newpass));
                        if ( $taille >= 5 && $taille <= 15 )
						{
							//On change le mot de passe.
							ChangerMotPasse($id,$newpass);
							$resultat='Le mot de passe a été changé.<br /><br />
							Il vous sera demandé lors de votre prochaine visite sur BisouLand.';
						}
						else
						{
							$resultat='Le nouveau mot de passe n\'a pas la bonne longueur';
						}
					}
					else
					{
						$resultat='Le nouveau mot de passe n\'est pas valide.';
					}
				}
				else
				{
					$resultat='Vous n\'avez pas rentré deux fois le même mot de passe.';
				}		
			}
			else
			{
				$resultat='Le mot de passe est éronné.';
			}
		}
		else
		{
			$resultat='Veuillez remplir tous les champs.';
		}
	}
	
	if (isset($resultat))
	{
		echo $resultat.'<br />';
	}
?>
<br />
<form method="post" class="formul" action="changepass.html">
<label>Mot de passe actuel: <br />
	<input type="password" name="oldpass" tabindex="40" size="15" maxlength="15" value=""/>
</label><br />
<label>Nouveau mot de passe: <br />
	<span class="petit">(Entre 5 et 15 caractères)</span><br />
	<input type="password" name="newpass" tabindex="50" size="15" maxlength="15" value=""/>
</label><br />
<label>Réécrivez le nouveau mot de passe: <br />
	<input type="password" name="newpass2" tabindex="60" size="15" maxlength="15" value=""/>
</label><br />

<input type="submit" name="changepswd" value="Changer le mot de passe" />
</form>

<?php

}
else
{
echo 'Tu n\'es pas connecté !!';
}

?>
