<?php

if ($_SESSION['logged'] == true)
{

	if (isset($_GET['idmsg']) && !empty($_GET['idmsg']))
	{
		$idmsg = htmlentities(addslashes($_GET['idmsg']));
		$retour = mysql_query("SELECT posteur, destin, message, timestamp, statut, titre FROM messages WHERE id='".$idmsg."'");
		$donnees = mysql_fetch_assoc($retour);
		if ($donnees['destin']==$_SESSION['id'])
		{
			if ($donnees['statut']==0)
			{
				mysql_query("UPDATE messages SET statut='1' WHERE id='".$idmsg."'");
			}
			$retour = mysql_query("SELECT pseudo FROM membres WHERE id='".$donnees['posteur']."'");
			$donnees2 = mysql_fetch_assoc($retour);
			$from =$donnees2['pseudo'];
			
			$objet = $donnees['titre'];
			$message = $donnees['message'];
			$dateEnvoie = $donnees['timestamp'];
?>

<a href="boite.html" title="Messages">Retour � la liste des messages</a>
<br />
<p>Auteur : <?php echo  stripslashes($from);?></p>
<p>Envoy� le <?php echo date('d/m/Y � H\hi', $dateEnvoie);?></p>
<p>Objet : <?php echo stripslashes($objet);?></p>
Message :<br />
<div class="message"><?php echo bbLow($message);?></div>
<?php
	if ($from!="BisouLand")
	{
?>
<form method="post" action="envoi.html">
	<input type="submit" tabindex="30" value="R�pondre" />
	<input type="hidden" name="titre" value="<?php echo 'RE:'.stripslashes($objet);?>" />
	<input type="hidden" name="destinataire" value="<?php echo stripslashes($from);?>" />
</form>
<?php
	}
?>
<form method="post" action="boite.html">
	<input type="submit" tabindex="30" value="Supprimer" />
	<input type="hidden" name="supprimer" value="<?php echo $idmsg;?>" />
</form>

<?php
		}
		else
		{
			echo 'Tu n\'as pas le droit de visionner ce message !!';
		}
	}
	else
	{
		echo 'Pas d\'id message sp�cifi�e !!';
	}
}
else
{
echo 'Tu n\'es pas connect� !!';
}
?>