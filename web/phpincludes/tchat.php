<h1>Messages du Mini Chat</h1>
<br />
<?php
$reponse = mysql_query("SELECT pseudo, message, timestamp FROM chatbisous ORDER BY id DESC");
				
while ($donnees = mysql_fetch_assoc($reponse) )
{
	$message=smileys($donnees['message']);
	echo '<p><strong>',$donnees['pseudo'],'</strong>, le ',date('d/m/Y à H\hi', $donnees['timestamp']),' :<br />',$message,'</p>';
}
?>