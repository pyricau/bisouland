<h1>.:Contact:.</h1>
<br />
L'auteur et administrateur de ce site est Pierre-Yves Ricau (<a href="http://www.piwai.info">http://www.piwai.info</a>).<br />
<br />
Contactez-moi pour plus d'informations sur BisouLand.<br />
<br />
Adresse email pour me joindre :  <a href="mailto:bisouland (arobase) piwai.info" title="mail">bisouland (arobase) piwai.info</a><br />
<br />
Pour toute question concernant le jeu, vous pouvez aussi me contacter via les messages privés,
en envoyant un message à
<?php 
if ($_SESSION['logged'] == true)
{
	echo 'admin. <a href="admin.envoi.html" title="Envoyer un message à l\'administrateur">Cliquez ici pour m\'envoyer un message.</a><br />';
}
else
{
	echo 'admin. (vous devez disposer d\'un compte et être connecté).<br />';
}
?>



