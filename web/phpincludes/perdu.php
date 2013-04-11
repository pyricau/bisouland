<?php
if ($_SESSION['logged'] == false)
{
$OK=false;
	if (isset($_POST['perdu']))
	{
		if (isset($_POST['Ppseudo'], $_POST['Pemail']) && !empty($_POST['Ppseudo']) && !empty($_POST['Pemail']) )
		{
			$Ppseudo=addslashes($_POST['Ppseudo']);
			$sql = mysql_query("SELECT email, id FROM membres WHERE pseudo='$Ppseudo'");
			if ($donnees = mysql_fetch_assoc($sql))
			{
				$email=$_POST['Pemail'];
				if ($donnees['email']==$email)
				{
					//On envoie le mail.
					$Pid=$donnees['id'];
					$cle=rand(1,100);
					$cleId=$cle+$Pid;
					mysql_query("UPDATE membres SET newpass=$cle WHERE id=$Pid");
					//Envoie du mail avec liens par GET avec $Pid et $cleId
					//***********
					//***********
					//Mène vers une autre page, ou l'on peut choisir son mot de passe : newpass.
					$lien='http://bisouland.piwai.info/'.$Pid.'.'.$cleId.'.newpass.html';
					$messageBrut='
					Ce courriel vous a &eacute;t&eacute; adress&eacute; suite à votre demande. Si ce n\'est pas le
cas, il s\'agit d\'une erreur de notre part et nous vous serions reconnaissant de
bien vouloir indiquer cette anomalie à l\'adresse suivante : <a href="mailto:bisouland@piwai.info">bisouland@piwai.info</a>
avant de supprimer ce mail. Nous vous en remercions par avance.<br />
					<br />
					Bonjour !!<br />
					Tu as perdu ton mot de passe sur BisouLand et tu désires en changer afin de pouvoir te connecter sur le site.<br />
					Il suffit pour cela de se rendre à l\'adresse suivante :<br /><br />
					<a href="'.$lien.'">'.$lien.'</a><br />
					<br />
					Si ce n\'est pas le cas, alors il te suffit de ne pas cliquer sur le lien.<br />
					<br />
					Amiti&eacute;s et beaucoup de Bisous<br />
					L\'équipe BisouLand<br />
					';
					
					$objet='BisouLand : Nouveau mot de passe';

					//-----------------------------------------------
//DECLARE LES VARIABLES
//-----------------------------------------------
$email_expediteur='bisouland@piwai.fr';
$email_reply='bisouland@piwai.fr';
$message_texte='Ce courriel vous a été adressé suite à votre demande. 
Si ce n\'est pas le cas, il s\'agit d\'une erreur de notre part et nous vous serions reconnaissant de bien vouloir indiquer cette anomalie à l\'adresse suivante : bisouland@piwai.info avant de supprimer ce mail.
Nous vous en remercions par avance.

Bonjour !!
Tu as perdu ton mot de passe sur BisouLand et tu désires en changer afin de pouvoir te connecter sur le site.
Il suffit pour cela de se rendre à l\'adresse suivante :

'.$lien.'

Si ce n\'est pas le cas, alors il te suffit de ne pas cliquer sur le lien.

Amitiés et beaucoup de Bisous
L\'équipe BisouLand
';

$message_html='<html>
<head>
<title>BisouLand : Changer de mot de passe</title>
</head>
<body>
<p>'.$messageBrut.'</p>
</body>
</html>';

//-----------------------------------------------
//GENERE LA FRONTIERE DU MAIL ENTRE TEXTE ET HTML
//-----------------------------------------------

$frontiere = '-----=' . md5(uniqid(mt_rand()));

//-----------------------------------------------
//HEADERS DU MAIL
//-----------------------------------------------

$headers = 'From: "BisouLand" <'.$email_expediteur.'>'."\n";
$headers .= 'Return-Path: <'.$email_reply.'>'."\n";
$headers .= 'MIME-Version: 1.0'."\n";
$headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

//-----------------------------------------------
//MESSAGE TEXTE
//-----------------------------------------------
$message = 'This is a multi-part message in MIME format.'."\n\n";

$message .= '--'.$frontiere."\n";
$message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
$message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
$message .= $message_texte."\n\n";

//-----------------------------------------------
//MESSAGE HTML
//-----------------------------------------------
$message .= '--'.$frontiere."\n";

$message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
$message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
$message .= $message_html."\n\n";

$message .= '--'.$frontiere.'--'."\n";

mail($email,$objet,$message,$headers);

$OK=true;
					
				}
				else
				{
					$resultat = "Ce n'est pas la bonne adresse email";
					$Ppseudo=stripslashes($_POST['Ppseudo']);
					$Pemail=$_POST['Pemail'];
				}
			}
			else
			{
				$resultat = "Ce joueur n'existe pas";
				$Ppseudo=stripslashes($_POST['Ppseudo']);
				$Pemail=$_POST['Pemail'];
			}
		}
		else
		{
			$resultat = "Il est n&eacute;cessaire de remplir tous les champs";
			$Ppseudo=$_POST['Ppseudo'];
			$Pemail=$_POST['Pemail'];
		}
	}
	else
	{
		$Ppseudo='';
		$Pemail='';
	}

if ($OK==false)
{
?>
<h1>Mot de passe perdu</h1>
<h2>Tu as perdu ton mot de passe ?</h2>
<h2>Pas de chance !!</h2>
N&eacute;anmoins, BisouLand te permet de changer de mot de passe par email.<br />
Il te suffit de remplir le formulaire qui suit.<br />

<?php	
	if (isset($resultat))
	{
		echo $resultat.'<br />';
	}
?>

<form method="post" class="formul" action="perdu.html">
	<label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caractères)</span><br /><input type="text" name="Ppseudo" tabindex="10" size="15" maxlength="15" value="<?php echo htmlentities($Ppseudo);?>"/></label><br />
    <label>Email : <br /><span class="petit">(Un mail d'activation du nouveau mot de passe te sera envoyé)</span><br /><input type="text" name="Pemail" tabindex="20" size="30" value="<?php echo htmlentities($Pemail);?>"/></label><br />
	<input type="submit" name="perdu" value="J'ai perdu mon mot de passe." />
</form>

<?php
}
else
{
	echo "Le mail vient de t'être envoyé.<br />";
}
}
else
{
	echo 'T\'es dja connected !!';
}
?>