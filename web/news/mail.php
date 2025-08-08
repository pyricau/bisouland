<?php

header('Content-type: text/html; charset=ISO-8859-1');

if (isset($_POST['objet']) && isset($_POST['message']) )
{


	include '../phpincludes/bd.php';
	bd_connect();

	$retour = mysql_query("SELECT email FROM membres WHERE confirmation='1' ORDER BY id");
	$destinataire = EMAIL_ADMIN;
	while ($donnees = mysql_fetch_array($retour))
	{
		$destinataire.=', '.$donnees['email'];
	}
	mail ($destinataire,$_POST['objet'],$_POST['message'],"From:".EMAIL_EXPEDITOR);
}

 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Envoyer un mail</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

   </head>
    
<body>


<p><a href="liste_news.php">Retourner aux news</a></p>
<p><a href="../index.php">Retourner sur bisouland.piwai.info</a></p>

<div class="formul">

<form method="post" action="mail.php">
		<p>
		<label>Objet du message<br />
		<input name="objet" tabindex="40" size="35" value=""/><br /></label>
        <label>Message<br />
        <textarea name="message" tabindex="50" rows="8" cols="50"></textarea><br /></label>
        <input type="submit" tabindex="60" value="Envoyer" />
		</p>
</form>

</body>
</html>
