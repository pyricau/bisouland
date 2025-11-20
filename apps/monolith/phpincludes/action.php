<h1>Embrasser</h1>
<?php
if (true === $_SESSION['logged']) {
    $pdo = bd_connect();
    if (isset($_POST['action'])) {
        $cout = 0;
        $nuageCible = htmlentities((string) $_POST['nuage']);
        $positionCible = htmlentities((string) $_POST['position']);

        if (0 == $joueurBloque) {
            if (($nbE[1][0] + $nbE[1][1] + $nbE[1][2]) > 0) {
                if (0 == $nuageCible || 0 == $positionCible) {
                    $resultat = 'Evite les valeurs nulles pour les deux champs';
                } else {
                    $stmt = $pdo->prepare('SELECT id, score FROM membres WHERE nuage = :nuage AND position = :position');
                    $stmt->execute(['nuage' => $nuageCible, 'position' => $positionCible]);
                    if ($donnees_info = $stmt->fetch()) {
                        $cible = $donnees_info['id'];
                        $score = $donnees_info['score'];

                        if ($cible == $id) {
                            $resultat = 'Il est impossible s\'attaquer soi même';
                        } else {
                            $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_id FROM evolution WHERE auteur = :auteur AND classe = 1');
                            $stmt->execute(['auteur' => $id]);
                            if (0 != $stmt->fetchColumn()) {
                                $resultat = 'Action impossible car tu es en train de créer des Bisous';
                            } else {
                                // On détermine s'il y a une construction en cours.
                                $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_id FROM liste WHERE auteur = :auteur AND classe = 1');
                                $stmt->execute(['auteur' => $id]);
                                if (0 != $stmt->fetchColumn()) {
                                    $resultat = 'Action impossible car tu es en train de créer des Bisous';
                                }

                                $nuageSource = $_SESSION['nuage'];

                                $stmt = $pdo->prepare('SELECT position, score FROM membres WHERE id = :id');
                                $stmt->execute(['id' => $id]);
                                $donnees_info = $stmt->fetch();

                                $positionSource = $donnees_info['position'];
                                $scoreSource = $donnees_info['score'];

                                $score = floor($score / 1000.);
                                $scoreSource = floor($scoreSource / 1000.);
                                $Niveau = voirNiveau($scoreSource, $score);

                                if (0 === $Niveau) {
                                    $distance = abs(16 * ($nuageCible - $nuageSource) + $positionCible - $positionSource);

                                    $distMax = distanceMax($nbE[0][0], $nbE[0][4]);

                                    if ($distance <= $distMax) {
                                        $cout = coutAttaque($distance, $nbE[0][4]);
                                        if ($amour >= $cout) {
                                            $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_att FROM logatt WHERE auteur = :auteur AND cible = :cible AND timestamp >= :timestamp');
                                            $stmt->execute(['auteur' => $id, 'cible' => $cible, 'timestamp' => time() - 43200]);
                                            if ($stmt->fetchColumn() < 3) {
                                                $amour -= $cout;
                                                $joueurBloque = 1;
                                                $duree = tempsAttaque($distance, $nbE[0][4]);
                                                $stmt = $pdo->prepare('UPDATE membres SET bloque = TRUE WHERE id = :id');
                                                $stmt->execute(['id' => $id]);
                                                $stmt = $pdo->prepare('INSERT INTO attaque VALUES (:auteur, :cible, :finaller, :finretour, 0)');
                                                $stmt->execute(['auteur' => $id, 'cible' => $cible, 'finaller' => time() + $duree, 'finretour' => time() + 2 * $duree]);
                                                AdminMP($cible, $pseudo." veut t'embrasser", $pseudo." vient d'envoyer ses bisous dans ta direction, et va tenter de t'embrasser.
						".$pseudo.' est situé sur le nuage '.$nuageSource.', à la position '.$positionSource.'.
						Ses Bisous arrivent dans '.strTemps($duree).'.');
                                                $resultat = 'Tes Bisous sont en route vers la position '.$positionSource.' du nuage '.$nuageSource.', ils arriveront à destination dans '.strTemps($duree).'.';
                                            } else {
                                                $resultat = "Il est impossible d'embrasser le même joueur plus de 3 fois toutes les 12 heures";
                                            }
                                        } else {
                                            $resultat = "Tu ne disposes pas d'assez de Points d'Amour";
                                        }
                                    } else {
                                        $resultat = 'Cette position est hors de portée';
                                    }
                                } else {
                                    $resultat = "Ce joueur n'est pas de ton niveau";
                                }
                            }
                        }
                    } else {
                        $resultat = 'Il n\'y a personne à la position indiquée';
                    }
                }
            }// Pas de bisous
            else {
                $resultat = "Tu ne disposes d'aucun Bisou";
            }
        }// joueur bloqué
        else {
            $resultat = 'Tu as déjà une cible';
        }
    } elseif (isset($_GET['nuage']) && isset($_GET['position'])) {
        $pdo = bd_connect();
        $nuageCible = htmlentities((string) $_GET['nuage']);
        $positionCible = htmlentities($_GET['position']);

        $nuageSource = $_SESSION['nuage'];

        $stmt = $pdo->prepare('SELECT position FROM membres WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $donnees_info = $stmt->fetch();
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
    if (0 == $joueurBloque) {
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
				<input type="text" name="nuage" maxlength="3" value="<?php echo $nuageCible; ?>" tabindex="20" size="2"/>
			</td>
        </tr>
		<tr>
			<td>
				Position
			</td>
			<td>
				<input type="text" name="position" maxlength="2" value="<?php echo $positionCible; ?>" tabindex="20" size="2"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Cette opération nécessite <?php echo $cout; ?> points d'amour.
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