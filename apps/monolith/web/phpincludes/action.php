<h1>Embrasser</h1>
<?php
if ($_SESSION['logged'] == true) {

    if (isset($_POST['action'])) {
        $cout = 0;
        $nuageCible = htmlentities($_POST['nuage']);
        $positionCible = htmlentities($_POST['position']);

        if ($joueurBloque == 0) {
            if (($nbE[1][0] + $nbE[1][1] + $nbE[1][2]) > 0) {
                if ($nuageCible == 0 || $positionCible == 0) {
                    $resultat = 'Evite les valeurs nulles pour les deux champs';
                } else {
                    $sql_info = mysql_query("SELECT id, score FROM membres WHERE nuage=".$nuageCible." AND position=".$positionCible);
                    if ($donnees_info = mysql_fetch_assoc($sql_info)) {
                        $cible = $donnees_info['id'];
                        $score = $donnees_info['score'];

                        if ($cible == $id) {
                            $resultat = 'Il est impossible s\'attaquer soi même';
                        } else {
                            $sql_info = mysql_query("SELECT COUNT(*) AS nb_id FROM evolution WHERE auteur=$id AND classe=1");
                            if (mysql_result($sql_info, 0, 'nb_id') != 0) {
                                $resultat = 'Action impossible car tu es en train de créer des Bisous';
                            } else {

                                //On détermine s'il y a une construction en cours.
                                $sql_info = mysql_query("SELECT COUNT(*) AS nb_id FROM liste WHERE auteur=$id AND classe=1");
                                if (mysql_result($sql_info, 0, 'nb_id') != 0) {
                                    $resultat = 'Action impossible car tu es en train de créer des Bisous';
                                }
                                {
                                    $nuageSource = $_SESSION['nuage'];

                                    $sql_info = mysql_query("SELECT position, score FROM membres WHERE id='".$id."'");
                                    $donnees_info = mysql_fetch_assoc($sql_info);

                                    $positionSource = $donnees_info['position'];
                                    $scoreSource = $donnees_info['score'];

                                    $score = floor($score / 1000.);
                                    $scoreSource = floor($scoreSource / 1000.);
                                    $Niveau = voirNiveau($scoreSource, $score);

                                    if ($Niveau == 0) {

                                        $distance = abs(16 * ($nuageCible - $nuageSource) + $positionCible - $positionSource);

                                        $distMax = distanceMax($nbE[0][0], $nbE[0][4]);

                                        if ($distance <= $distMax) {
                                            $cout = coutAttaque($distance, $nbE[0][4]);
                                            if ($amour >= $cout) {

                                                $sql = mysql_query("SELECT COUNT(*) AS nb_att FROM logatt WHERE auteur=$id AND cible=$cible AND timestamp>=".(time() - 43200));
                                                if (mysql_result($sql, 0, 'nb_att') < 3) {
                                                    $amour -= $cout;
                                                    $joueurBloque = 1;
                                                    $duree = tempsAttaque($distance, $nbE[0][4]);
                                                    mysql_query("UPDATE membres SET bloque=1 WHERE id='".$id."'");
                                                    mysql_query("INSERT INTO attaque VALUES (".$id.", ".$cible.", ".(time() + $duree).", ".(time() + 2 * $duree).", 0)");
                                                    AdminMP($cible, $pseudo." veut t'embrasser", $pseudo." vient d'envoyer ses bisous dans ta direction, et va tenter de t'embrasser.
						".$pseudo." est situé sur le nuage ".$nuageSource.", à la position ".$positionSource.".
						Ses Bisous arrivent dans ".strTemps($duree).".");
                                                    $resultat = 'Tes Bisous sont en route vers la position '.$positionSource.' du nuage '.$nuageSource.', ils arriveront à destination dans '.strTemps($duree).'.';
                                                } else {
                                                    $resultat = "Il est impossible d'embrasser le même joueur plus de 3 fois toutes les 12 heures";
                                                }
                                            } else {
                                                $resultat = 'Tu ne disposes pas d\'assez de Points d\'Amour';
                                            }
                                        } else {
                                            $resultat = 'Cette position est hors de portée';
                                        }
                                    } else {
                                        $resultat = "Ce joueur n'est pas de ton niveau";
                                    }
                                }
                            }
                        }
                    } else {
                        $resultat = 'Il n\'y a personne à la position indiquée';
                    }
                }
            }//Pas de bisous
            else {
                $resultat = 'Tu ne disposes d\'aucun Bisou';
            }
        }//joueur bloqué
        else {
            $resultat = 'Tu as déjà une cible';
        }
    } elseif (isset($_GET['nuage']) && isset($_GET['position'])) {
        $nuageCible = htmlentities($_GET['nuage']);
        $positionCible = htmlentities($_GET['position']);

        $nuageSource = $_SESSION['nuage'];

        $sql_info = mysql_query("SELECT position FROM membres WHERE id='".$id."'");
        $donnees_info = mysql_fetch_assoc($sql_info);
        $positionSource = $donnees_info['position'];

        $distance = abs(16 * ($nuageCible - $nuageSource) + $positionCible - $positionSource);

        $cout = coutAttaque($distance, $nbE[0][4]);

    } else {
        $nuageCible = 0;
        $positionCible = 0;
        $cout = 0;
    }
    if (isset($resultat)) {
        echo '<span class="info">[ '.$resultat.' ]</span><br /><br />';
    }
    if ($joueurBloque == 0) {
        ?>
<center>
<form method="post" action="action.html">
<table>
	<tbody>
		<tr>
			<th colspan="2">Cible</th>
		</tr>
		<tr>
			<td>
				Nuage
			</td>
			<td>
				<input type="text" name="nuage" maxlength="3" value="<?php echo $nuageCible;?>" tabindex="20" size="2"/>
			</td>
        </tr>
		<tr>
			<td>
				Position
			</td>
			<td>
				<input type="text" name="position" maxlength="2" value="<?php echo $positionCible;?>" tabindex="20" size="2"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Cette opération nécessite <?php echo $cout;?> points d'amour.
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="action" value="Go !!" />
			</td>
		</tr>
    </tbody>
</table>
</form>
</center>
<br />

<?php
    } else {
        echo 'Tes Bisous sont en chemin.<br />';
    }
} else {
    echo 'Tu n\'es pas connecté !!';
}
?>