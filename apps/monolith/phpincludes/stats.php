<?php

$pdo = bd_connect();

$retour = $pdo->query('SELECT SUM( amour ) AS nb FROM membres WHERE confirmation = TRUE');
$pointsAmourTotal = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '5 minutes'");
$connectCinq = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '1 hour'");
$connectHeure = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '12 hours'");
$connectMid = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '24 hours'");
$connectJour = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '48 hours'");
$connect2Jour = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '7 days'");
$connectSemaine = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '30 days'");
$connectMois = $retour->fetchColumn();
$retour = $pdo->query("SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect >= CURRENT_TIMESTAMP - INTERVAL '1 year'");
$connectAn = $retour->fetchColumn();
?>
<h1>Statistiques</h1>
<span class="info">[ Statistiques à compter du 26 avril 2006 ]</span><br />
<br />
<br />
Nombre total de points d'amours disponibles dans le jeu : <?php echo formaterNombre($pointsAmourTotal); ?><br />
<br />
Nombre de points d'amours moyen par personne : <?php echo formaterNombre($pointsAmourTotal / $connectAn); ?><br />
<br />
Nombre de membres connectés dans les dernières 5 minutes : <?php echo $connectCinq; ?><br />
<br />
Nombre de membres connectés dans les dernières 60 minutes : <?php echo $connectHeure; ?><br />
<br />
Nombre de membres connectés dans les dernières 12 heures : <?php echo $connectMid; ?><br />
<br />
Nombre de membres connectés dans les dernières 24 heures : <?php echo $connectJour; ?><br />
<br />
Nombre de membres connectés dans les dernières 48 heures : <?php echo $connect2Jour; ?><br />
<br />
Nombre de membres connectés dans les derniers 7 jours : <?php echo $connectSemaine; ?><br />
<br />
Nombre de membres connectés dans les derniers 30 jours : <?php echo $connectMois; ?><br />
<br />
Nombre de membres connectés depuis un an : <?php echo $connectAn; ?><br />
<br />
