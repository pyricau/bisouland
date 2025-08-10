<h1>Cerveau</h1>
<?php
if ($_SESSION['logged'] == true) {
$production = calculerGenAmour(0, 3600, $nbE[0][0], $nbE[1][0], $nbE[1][1], $nbE[1][2]);

$sql_info = mysql_query("SELECT score FROM membres WHERE id=".$id);
$donnees_info = mysql_fetch_assoc($sql_info);
$score = floor($donnees_info['score'] / 1000.);

$sql = mysql_query("SELECT COUNT(*) AS position FROM membres WHERE score>".$donnees_info['score']);
$position = mysql_result($sql, 0, 'position') + 1;

$sql = mysql_query("SELECT COUNT(*) AS nb_joueur FROM membres WHERE confirmation='1'");
$totalJoueur = mysql_result($sql, 0, 'nb_joueur');

?>
Score : <strong><?php echo formaterNombre($score); ?></strong> Point<?php echo pluriel($score);?><br />
<br />
Classement : <strong><?php echo $position;
if ($position == 1) {
echo 'er';
} else {
echo 'ème';
}?> / <?php echo $totalJoueur;?></strong><br />
<br />
R&eacute;serves : <strong><?php echo formaterNombre(floor($amour)); ?></strong> Point<?php echo pluriel(floor($amour));?> d'Amour<br />
<br />
Production : <strong><?php echo formaterNombre(floor($production)); ?></strong> Point<?php echo pluriel(floor($production));?> d'Amour par heure<br />
<br />
<?php

//On récupère les infos sur le joueur que l'on attaque.
$sql_info = mysql_query("SELECT cible, finaller, finretour, butin FROM attaque WHERE auteur=".$id);

if ($donnees_info = mysql_fetch_assoc($sql_info)) {

    $sql_info2 = mysql_query("SELECT pseudo, nuage, position FROM membres WHERE id=".$donnees_info['cible']);
    $donnees_info2 = mysql_fetch_assoc($sql_info2);
    $pseudoCible = $donnees_info2['pseudo'];
    $nuageCible = $donnees_info2['nuage'];
    $positionCible = $donnees_info2['position'];
    $finAll = $donnees_info['finaller'];
    $finRet = $donnees_info['finretour'];
    $butinPris = $donnees_info['butin'];

    if (isset($_POST['cancelAttaque'])) {
        if ($finAll != 0) {
            $finRet = (2 * time() + $finRet - 2 * $finAll);
            $finAll = 0;
            mysql_query("UPDATE attaque SET finaller=0, finretour=$finRet WHERE auteur=".$id);
            AdminMP($donnees_info['cible'], "Attaque annulée", "$pseudo a annulé son attaque.
			Tu n'es plus en danger.");
        }
    }

    if ($finAll != 0) {

?>
Tu vas tenter d'embrasser <strong><?php echo $pseudoCible; ?></strong> sur le nuage <strong><?php echo $nuageCible; ?></strong>
 &agrave; la position <strong><?php echo $positionCible; ?></strong>.<br /><br />
Tes bisous atteindront <strong><?php echo $pseudoCible; ?></strong> dans : 
	<script src="includes/compteur.js" type="text/javascript"></script>
	<span id="compteur"><?php echo strTemps($finAll - time()); ?></span>
	<script language="JavaScript">
		duree="<?php echo($finAll - time());?>";
		stop="";
		fin="";
		next="Termin&eacute;";
		adresseStop="";
		adresseFin="cerveau.html";
		
		duree2="<?php echo($finRet - time());?>";
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
		duree="<?php echo($finRet - time());?>";
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
//Infos sur les joueurs qui nous attaquent.
$sql_info = mysql_query("SELECT auteur, finaller FROM attaque WHERE cible=".$id." AND finaller!=0 ORDER BY finaller");
while ($donnees_info = mysql_fetch_assoc($sql_info)) {
    $sql_info2 = mysql_query("SELECT pseudo, nuage, position FROM membres WHERE id=".$donnees_info['auteur']);
    $donnees_info2 = mysql_fetch_assoc($sql_info2);
    $pseudoAuteur = $donnees_info2['pseudo'];
    $nuageAuteur = $donnees_info2['nuage'];
    $positionAuteur = $donnees_info2['position'];
    $finAll = $donnees_info['finaller'];

?>
Pr&eacute;pare toi : <strong><?php echo $pseudoAuteur; ?></strong> va essayer de t'embrasser 
depuis le nuage <strong><?php echo $nuageAuteur; ?></strong>, 
&agrave; la position <strong><?php echo $positionAuteur; ?></strong>, 
dans <strong><?php echo strTemps($finAll - time()); ?></strong>.<br />


<?php
}//Fin du while

}//Fin du login true
else {
    echo 'Tu n\'es pas connecté !!';
}
?>
