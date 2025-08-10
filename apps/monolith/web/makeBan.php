<?php

include 'phpincludes/bd.php';
bd_connect();

//On récupère le nombre de joueurs
$sql = mysql_query("SELECT COUNT(*) AS nb_joueur FROM membres WHERE confirmation='1'");
$totalJoueur = mysql_result($sql, 0, 'nb_joueur');

$requete = "
SELECT ban.auteur AS auteur,
ban.id AS id,
membres.score AS score,
membres.pseudo AS pseudo
FROM ban
LEFT JOIN membres
ON ban.auteur = membres.id
";
$i = 0;
$sql = mysql_query($requete);
while ($donnees = mysql_fetch_assoc($sql)) {
    $i++;
    $pseudo = $donnees['pseudo'];
    echo $pseudo . "<br />";//Pour les tests.

    //On calcule le rang du joueur.
    $sql2 = mysql_query("SELECT COUNT(*) AS position FROM membres WHERE score>" . $donnees['score']);
    $position = mysql_result($sql2, 0, 'position') + 1;

    $txtpos = $position . '/' . $totalJoueur;

    //On calcule le score du joueur.
    $score = floor($donnees['score'] / 1000.);

    //Création de l'image :
    $image = imagecreatefromjpeg("images/imgban.jpg");

    $police = imageloadfont('polices/small.gdf');
    $police2 = imageloadfont('polices/pseudo.gdf');

    $C = imagecolorallocate($image, 255, 50, 50);
    $C2 = imagecolorallocate($image, 255, 0, 0);

    imagestring($image, $police2, 235, 5, $pseudo, $C2);

    imagestring($image, $police, 344, 41, $score, $C);
    imagestring($image, $police, 366, 65, $txtpos, $C);

    imagepng($image, 'ban/' . $donnees['id'] . '.png');
}
echo $i . " images.<br />";
