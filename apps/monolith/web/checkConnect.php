<?php

include 'phpincludes/fctIndex.php';
include 'phpincludes/bd.php';

bd_connect();

$tempsUneSemaine = time() - 604800;
$tempsUnMois = time() - 2678400;

//Si les joueurs se sont connectes depuis un mois, on enleve l'avertissement.
$sql = mysql_query("SELECT id FROM membres WHERE averto>0 AND lastconnect>$tempsUnMois");
while ($donnees = mysql_fetch_assoc($sql)) {
    mysql_query("UPDATE membres SET averto=0 WHERE id=".$donnees['id']);
}

$sql = mysql_query("SELECT id,averto,confirmation FROM membres WHERE lastconnect<$tempsUnMois");

//Boucle de traitement des informations.
while ($donnees = mysql_fetch_assoc($sql)) {

    if ($donnees['id'] != 1) {
        //Si compte non confirme, on le supprime. (et que c'est po BisouLand)
        if ($donnees['confirmation'] == 0) {
            SupprimerCompte($donnees['id']);
        } else {
            //Si pas encore eu d'avertissement :
            if ($donnees['averto'] == 0) {
                mysql_query("UPDATE membres SET averto=".time()." WHERE id=".$donnees['id']);
            } elseif ($donnees['averto'] < $tempsUneSemaine) {
                //On va pas embeter l'utilisateur, s'il n'a pas repondu a l'avertissement, on supprime son compte.
                SupprimerCompte($donnees['id']);
                $i++;
            }
        }
    }
}
