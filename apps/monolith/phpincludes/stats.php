<?php

$pdo = bd_connect();

$retour = $pdo->query('SELECT SUM( amour ) AS nb FROM membres WHERE confirmation = TRUE');
$pointsAmourTotal = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 300));
$connectCinq = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 3600));
$connectHeure = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 43200));
$connectMid = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 86400));
$connectJour = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 172800));
$connect2Jour = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 604800));
$connectSemaine = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 2635200));
$connectMois = $retour->fetchColumn();
$retour = $pdo->query('SELECT COUNT(*) AS nb FROM membres WHERE confirmation=TRUE AND lastconnect>='.(time() - 31536000));
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
