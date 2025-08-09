<?php

$retour = mysql_query("SELECT SUM( amour ) AS nb FROM membres WHERE confirmation = 1");
$pointsAmourTotal=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-300));
$connectCinq=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-3600));
$connectHeure=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-43200));
$connectMid=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-86400));
$connectJour=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-172800));
$connect2Jour=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-604800));
$connectSemaine=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-2635200));
$connectMois=mysql_result($retour, 0, 'nb');
$retour = mysql_query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=1 AND lastconnect>=".(time()-31536000));
$connectAn=mysql_result($retour, 0, 'nb');
?>
<h1>Statistiques</h1>
<span class="info">[ Statistiques à compter du 26 avril 2006 ]</span><br />
<br />
<br />
Nombre total de points d'amours disponibles dans le jeu : <?php echo formaterNombre($pointsAmourTotal)?><br />
<br />
Nombre de points d'amours moyen par personne : <?php echo formaterNombre($pointsAmourTotal/$connectAn)?><br />
<br />
Nombre de membres connectés dans les dernières 5 minutes : <?php  echo $connectCinq;?><br />
<br />
Nombre de membres connectés dans les dernières 60 minutes : <?php  echo $connectHeure;?><br />
<br />
Nombre de membres connectés dans les dernières 12 heures : <?php  echo $connectMid;?><br />
<br />
Nombre de membres connectés dans les dernières 24 heures : <?php  echo $connectJour;?><br />
<br />
Nombre de membres connectés dans les dernières 48 heures : <?php  echo $connect2Jour;?><br />
<br />
Nombre de membres connectés dans les derniers 7 jours : <?php  echo $connectSemaine;?><br />
<br />
Nombre de membres connectés dans les derniers 30 jours : <?php  echo $connectMois;?><br />
<br />
Nombre de membres connectés depuis un an : <?php  echo $connectAn;?><br />
<br />
