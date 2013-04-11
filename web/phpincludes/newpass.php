<?php

if ($_SESSION['logged'] == false)
{

$affich=false;

if (isset($_GET['Cid'], $_GET['Ccle']) && !empty($_GET['Cid']) && !empty($_GET['Ccle']) )
{
	$resultat='Tu n\'as rien a faire ici :D';
	$Cid=htmlentities(addslashes($_GET['Cid']));
	$Ccle=htmlentities(addslashes($_GET['Ccle']));
	$cle=$Ccle-$Cid;
	$sql = mysql_query("SELECT newpass FROM membres WHERE id=$Cid");
	if ($donnees = mysql_fetch_assoc($sql))
	{
		if ($donnees['newpass']==$cle && $donnees['newpass']!=0)
		{
			$affich=true;
			$resultat='';
		}
	}

}
elseif (isset ($_POST['newpswd']))
{
	$resultat='Tu n\'as rien a faire ici :D';
	if (isset ($_POST['Cid'],$_POST['Ccle']) && !empty($_POST['Cid']) && !empty($_POST['Ccle']) )
	{
	
		$Cid=htmlentities(addslashes($_POST['Cid']));
		$Ccle=htmlentities(addslashes($_POST['Ccle']));
		$cle=$Ccle-$Cid;
		$sql = mysql_query("SELECT newpass FROM membres WHERE id=$Cid");
		if ($donnees = mysql_fetch_assoc($sql))
		{	
		if ($donnees['newpass']==$cle && $donnees['newpass']!=0)
		{
		
			$affich=true;
			
			if (isset ($_POST['newpass'],$_POST['newpass2']) && !empty($_POST['newpass']) && !empty($_POST['newpass2']))
			{
				$newpass=$_POST['newpass'];
				$newpass2=$_POST['newpass2'];
				if ($newpass==$newpass2)
				{
					if (preg_match("!^\w+$!", $newpass))
					{
						$newpass=htmlentities(addslashes($_POST['newpass']));//Normalement inutile.
						$taille = strlen(trim($newpass));
                        if ( $taille >= 5 && $taille <= 15 )
						{
							//On change le mot de passe.
							ChangerMotPasse($Cid,$newpass);
							mysql_query("UPDATE membres SET newpass=0 WHERE id=$Cid");
							$resultat='Le mot de passe a été changé.<br /><br />
							Il te sera demandé lors de ta prochaine visite sur BisouLand.';
							$affich=false;
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
					$resultat='Tu n\'as pas rentré deux fois le même mot de passe.';
				}		
			}
			else
			{
				$resultat='Certains champs sont incomplets.';
			}
		}
		}
	}
}

if ($affich==true)
{
?>
<h1>Changer de mot de passe</h1>

<h2>Choisis un nouveau mot de passe</h2>

<?php	
	if (isset($resultat))
	{
		echo $resultat.'<br />';
	}
?>
<br />
<form method="post" class="formul" action="newpass.html">
<label>Nouveau mot de passe: <br />
	<span class="petit">(Entre 5 et 15 caractères)</span><br />
	<input type="password" name="newpass" tabindex="50" size="15" maxlength="15" value=""/>
</label><br />
<label>Réécris le nouveau mot de passe: <br />
	<input type="password" name="newpass2" tabindex="60" size="15" maxlength="15" value=""/>
	<input type="hidden" name="Cid" value="<?php echo $Cid;?>"/>
	<input type="hidden" name="Ccle" value="<?php echo $Ccle;?>"/>
</label><br />

<input type="submit" name="newpswd" value="Changer le mot de passe" />
</form>

<?php
}
else
{
	echo $resultat.'<br />';
}

}
else
{
	echo 'T\'es dja connect&eacute; !!';
}

?>