<h1>Cerveau</h1>
<?php

use Bl\Domain\KissBlowing\BlownKissState;

if (true === $blContext['is_signed_in']) {
$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();

$production = calculerGenAmour(0, 3600, $nbE[0][0], $nbE[1][0], $nbE[1][1], $nbE[1][2]);

$stmt = $pdo->prepare('SELECT score FROM membres WHERE id = :id');
$stmt->execute(['id' => $id]);
$donnees_info = $stmt->fetch();
$score = floor($donnees_info['score'] / 1000.);

$stmt = $pdo->prepare('SELECT COUNT(*) AS position FROM membres WHERE score > :score');
$stmt->execute(['score' => $donnees_info['score']]);
$position = $stmt->fetchColumn() + 1;

$sql = $pdo->query('SELECT COUNT(*) AS nb_joueur FROM membres WHERE confirmation = TRUE');
$totalJoueur = $sql->fetchColumn();

?>
Score : <strong><?php echo formaterNombre($score); ?></strong> Point<?php echo pluriel($score); ?><br />
<br />
Classement : <strong><?php echo $position;
if (1 == $position) {
echo 'er';
} else {
echo 'ème';
}?> / <?php echo $totalJoueur; ?></strong><br />
<br />
R&eacute;serves : <strong><?php echo formaterNombre(floor($amour)); ?></strong> Point<?php echo pluriel(floor($amour)); ?> d'Amour<br />
<br />
Production : <strong><?php echo formaterNombre(floor($production)); ?></strong> Point<?php echo pluriel(floor($production)); ?> d'Amour par heure<br />
<br />
<?php

// On récupère les infos sur le joueur que l'on attaque.
$stmt = $pdo->prepare('SELECT cible, finaller, finretour, butin, state FROM attaque WHERE auteur = :auteur');
$stmt->execute(['auteur' => $id]);

if ($donnees_info = $stmt->fetch()) {
    $stmt2 = $pdo->prepare('SELECT pseudo, nuage, position FROM membres WHERE id = :id');
    $stmt2->execute(['id' => $donnees_info['cible']]);
    $donnees_info2 = $stmt2->fetch();
    $pseudoCible = $donnees_info2['pseudo'];
    $nuageCible = $donnees_info2['nuage'];
    $positionCible = $donnees_info2['position'];
    $finAll = $castToUnixTimestamp->fromPgTimestamptz($donnees_info['finaller']);
    $finRet = $castToUnixTimestamp->fromPgTimestamptz($donnees_info['finretour']);
    $butinPris = $donnees_info['butin'];
    $state = BlownKissState::from($donnees_info['state']);

    if (isset($_POST['cancelAttaque']) && BlownKissState::EnRoute === $state) {
        $finRet = (2 * time() + $finRet - 2 * $finAll);
        $stmt3 = $pdo->prepare("UPDATE attaque SET state = 'CalledOff', finretour = :finretour WHERE auteur = :auteur");
        $stmt3->execute(['finretour' => $castToPgTimestamptz->fromUnixTimestamp($finRet), 'auteur' => $id]);
        AdminMP($donnees_info['cible'], 'Attaque annulée', "{$pseudo} a annulé son attaque.
			Tu n'es plus en danger.");
        $state = BlownKissState::CalledOff; // Update local variable to reflect the change
    }

    if (BlownKissState::EnRoute === $state) {
?>
Tu vas tenter d'embrasser <strong><?php echo $pseudoCible; ?></strong> sur le nuage <strong><?php echo $nuageCible; ?></strong>
 &agrave; la position <strong><?php echo $positionCible; ?></strong>.<br /><br />
Tes bisous atteindront <strong><?php echo $pseudoCible; ?></strong> dans :
	<script src="includes/compteur.js" type="text/javascript"></script>
	<span id="compteur"><?php echo strTemps($finAll - time()); ?></span>
	<script language="JavaScript">
		duree="<?php echo $finAll - time(); ?>";
		stop="";
		fin="";
		next="Termin&eacute;";
		adresseStop="";
		adresseFin="cerveau.html";

		duree2="<?php echo $finRet - time(); ?>";
		stop2="";
		fin2="";
		next2="Termin&eacute;";
		adresseStop2="";
		adresseFin2="cerveau.html";

		nbCompteur=2;

		t();
	</script>
<br />
<br />
Ils seront de retour dans : <span id="compteur2"><?php echo strTemps($finRet - time()); ?></span>
<br />
<br />
<form method="post" action="cerveau.html">
	<input type="submit" name="cancelAttaque" value="Annuler l'attaque" />
</form>
<?php
    } else {
?>
Tes bisous ont tent&eacute; d'embrasser <strong><?php echo $pseudoCible; ?></strong> sur le nuage <strong><?php echo $nuageCible; ?></strong>
 &agrave; la position <strong><?php echo $positionCible; ?></strong>.<br /><br />
Ils seront de retour dans :
	<script src="includes/compteur.js" type="text/javascript"></script>
	<span id="compteur"><?php echo strTemps($finRet - time()); ?></span>
	<script language="JavaScript">
		duree="<?php echo $finRet - time(); ?>";
		stop="";
		fin="";
		next="Termin&eacute;";
		adresseStop="";
		adresseFin="cerveau.html";
		nbCompteur=1;
		t();
	</script>
<br />
Ils ont pris &agrave; <strong><?php echo $pseudoCible; ?></strong> <strong><?php echo formaterNombre($butinPris); ?></strong> Points d'Amour.
<?php
    }
}
// Infos sur les joueurs qui nous attaquent.
$stmt = $pdo->prepare("SELECT auteur, finaller FROM attaque WHERE cible = :cible AND state = 'EnRoute' ORDER BY finaller");
$stmt->execute(['cible' => $id]);
while ($donnees_info = $stmt->fetch()) {
    $stmt2 = $pdo->prepare('SELECT pseudo, nuage, position FROM membres WHERE id = :id');
    $stmt2->execute(['id' => $donnees_info['auteur']]);
    $donnees_info2 = $stmt2->fetch();
    $pseudoAuteur = $donnees_info2['pseudo'];
    $nuageAuteur = $donnees_info2['nuage'];
    $positionAuteur = $donnees_info2['position'];
    $finAll = $castToUnixTimestamp->fromPgTimestamptz($donnees_info['finaller']);

?>
Pr&eacute;pare toi : <strong><?php echo $pseudoAuteur; ?></strong> va essayer de t'embrasser
depuis le nuage <strong><?php echo $nuageAuteur; ?></strong>,
&agrave; la position <strong><?php echo $positionAuteur; ?></strong>,
dans <strong><?php echo strTemps($finAll - time()); ?></strong>.<br />


<?php
}// Fin du while
}// Fin du login true
else {
    echo 'Tu n\'es pas connecté !!';
}
?>
