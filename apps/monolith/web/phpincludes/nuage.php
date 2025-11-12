<h1>Nuages</h1>
<?php
if (true == $_SESSION['logged']) {
    $pdo = bd_connect();

    // Infos sur le joueur.
    $nuageSource = $_SESSION['nuage'];
    $stmt = $pdo->prepare('SELECT position, score FROM membres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $donnees_info = $stmt->fetch();
    $positionSource = $donnees_info['position'];
    $scoreSource = floor($donnees_info['score'] / 1000.);

    $sql_info = $pdo->query('SELECT nombre FROM nuage WHERE id=1');
    $donnees_info = $sql_info->fetch();
    $NbNuages = $donnees_info['nombre'];

    if (isset($_POST['nuage']) && !empty($_POST['nuage'])) {
        $nuageL = htmlentities((string) $_POST['nuage']);
        if ($nuageL < 1) {
            $nuageL = 1;
        }
        if ($nuageL > $NbNuages) {
            $nuageL = $NbNuages;
        }
    } elseif (isset($_GET['nuage']) && !empty($_GET['nuage'])) {
        $nuageL = htmlentities((string) $_GET['nuage']);
        if ($nuageL < 1) {
            $nuageL = 1;
        }
        if ($nuageL > $NbNuages) {
            $nuageL = $NbNuages;
        }
    } elseif (isset($_GET['saut']) && !empty($_GET['saut']) && isset($_GET['sautnuage']) && !empty($_GET['sautnuage'])) {
        $nuageL = htmlentities((string) $_GET['sautnuage']);
        if ($nuageL > 0) {
            if ($nuageL <= $NbNuages) {
                if (isset($_GET['sautposition']) && !empty($_GET['sautposition'])) {
                    $positionCible = $_GET['sautposition'];

                    if ($positionCible > 0 && $positionCible < 17) {
                        // Au moins saut niveau 1.
                        if ($nbE[2][3] > 0) {
                            $distance = abs(16 * ($nuageL - $nuageSource) + $positionCible - $positionSource);
                            // On prend en compte les jambes, et le niveau de saut.
                            $distMax = distanceMax($nbE[0][4], $nbE[2][3]);
                            if ($distance <= $distMax) {
                                // Vérifions si il ya quelqu'un :
                                $stmt = $pdo->prepare('SELECT id FROM membres WHERE nuage = :nuage AND position = :position');
                                $stmt->execute(['nuage' => $nuageL, 'position' => $positionCible]);
                                if ($donnees_info = $stmt->fetch()) {
                                    $resultat = 'La position est déjà occupée';
                                } elseif (0 == $joueurBloque) {
                                    $stmt = $pdo->prepare('SELECT auteur FROM attaque WHERE cible = :cible AND finaller != 0');
                                    $stmt->execute(['cible' => $id]);
                                    if ($donnees_info = $stmt->fetch()) {
                                        $resultat = "Tu ne peux pas sauter car quelqu'un tente de t'embrasser";
                                    } else {
                                        $ajout = $nbE[0][0] + 0.3 * $nbE[1][0] + 0.6 * $nbE[1][1] + $nbE[1][2];
                                        // A modifier si on modifie calcul amour, car il est basé dessu.
                                        $cout = expo(20, 0.1, $ajout) * (1 + 0.1 * $distance);
                                        if ($amour >= $cout) {
                                            $amour -= $cout;
                                            $stmt = $pdo->prepare('UPDATE membres SET nuage = :nuage, position = :position WHERE id = :id');
                                            $stmt->execute(['nuage' => $nuageL, 'position' => $positionCible, 'id' => $id]);
                                            $_SESSION['nuage'] = $nuageL;
                                            $nuageSource = $nuageL;
                                            $positionSource = $positionCible;
                                            $resultat = 'Saut effectué, tu as utilisé '.ceil($cout)." Points d'Amour";
                                        } else {
                                            $resultat = "Tu ne disposes pas d'assez de Points d'Amour : il faut ".ceil($cout)." Points d'Amour.";
                                        }
                                    }
                                } else {
                                    $resultat = "Tu tentes d'embrasser quelqu'un, tu ne peux pas sauter";
                                }
                            } else {
                                $resultat = 'Distance trop grande';
                            }
                        } else {
                            $resultat = 'Niveau de saut insuffisant';
                        }
                    } else {
                        $resultat = 'Position : valeur hors intervalle';
                    }
                } else {
                    $resultat = 'Position non définie';
                }
            } else {
                $nuageL = $NbNuages;
                $resultat = 'Il n\'existe pas de nuage supérieur à '.$NbNuages;
            }
        } else {
            $nuageL = 1;
            $resultat = 'Il n\'existe pas de nuage inférieur à 1';
        }
    } else {
        $nuageL = $_SESSION['nuage'];
    }

    $Suivant = $nuageL + 1;
    if ($Suivant > $NbNuages) {
        $Suivant = $NbNuages;
    }
    $Precedent = $nuageL - 1;
    if ($Precedent < 1) {
        $Precedent = 1;
    }

    if (isset($resultat)) {
        echo '<center><span class="info">[ '.$resultat.' ]</span></center><br /><br />';
    }
    ?>

<center><table>
	<tbody>
		<tr>
			<th colspan="3">Nuage</th>
		</tr>
		<tr>
			<td>
				<form method="post" action="nuage.html">
				<input type="hidden" name="nuage" value="<?php echo $Precedent; ?>" />
				<input type="submit" name="bouton" value="&lt;-" />
				</form>
			</td>
			<td>
				<form method="post" action="nuage.html">
				<input type="text" name="nuage" maxlength="3" value="<?php echo $nuageL; ?>" tabindex="50" size="2"/>
				<input type="submit" name="bouton" value="Voir" />
				</form>
			</td>
			<td>
				<form method="post" action="nuage.html">
				<input type="hidden" name="nuage" value="<?php echo $Suivant; ?>" />
				<input type="submit" name="bouton" value="-&gt" />
				</form>
			</td>
        </tr>
    </tbody>
</table></center>
<br />
<center>
<?php
if ($scoreSource < 50) {
    echo '<span class="info">[ Il faut plus de 50 points de score pour embrasser ]</span><br /><br />';
} elseif (1 == $joueurBloque) {
    echo '<span class="info">[ Rappel : Tu es train de tenter d\'embrasser un joueur ]</span><br /><br />';
} elseif (($nbE[1][0] + $nbE[1][1] + $nbE[1][2]) == 0) {
    echo '<span class="info">[ Rappel : Tu n\'as pas de Bisous pour embrasser]</span><br /><br />';
}
    if (0 == $nbE[2][3]) {
        echo '<span class="info">[ Il te faut la technique Saut pour pouvoir sauter]</span><br /><br />';
    }
    ?>
<table width="80%">
   <tr>
       <th width="10%">Position</th>
		<th width="5%"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/onoff.png" alt="Statut" title="" /><span>Statut de connexion du joueur</span></a></th>
       <th width="60%">Nom</th>
       <th width="30%">Actions</th>
   </tr>
<?php

        $distMax = distanceMax($nbE[0][0], $nbE[0][4]);

    $stmt = $pdo->prepare('SELECT id, pseudo, position, lastconnect, score FROM membres WHERE nuage = :nuage ORDER BY position ASC');
    $stmt->execute(['nuage' => $nuageL]);
    $donnees_info = $stmt->fetch();
    for ($i = 1; $i <= 16; ++$i) {
        if ($donnees_info['position'] == $i) {
            $donnees_info['pseudo'] = stripslashes((string) $donnees_info['pseudo']);
            echo '<tr><td>',$i,'</td><td>';
            if ($donnees_info['lastconnect'] > time() - 300) {
                echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>'.$donnees_info['pseudo'].' est connect&eacute;</span></a> ';
            } else {
                echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>'.$donnees_info['pseudo']." n'est pas connect&eacute;</span></a> ";
            }
            echo '</td><td>';
            if ($donnees_info['id'] != $id) {
                $score = floor($donnees_info['score'] / 1000.);
                $Niveau = voirNiveau($scoreSource, $score);
                if (1 == $Niveau) {
                    if ($score >= 50) {
                        echo '<a class="bulle" style="cursor: default;color:blue;" onclick="return false;" href=""><strong>',$donnees_info['pseudo'],'</strong><span style="color:blue;">Joueur trop faible</span>';
                    } else {
                        echo '<a class="bulle" style="cursor: default;color:teal;" onclick="return false;" href=""><strong>',$donnees_info['pseudo'],'</strong><span style="color:teal;">Joueur ayant moins de 50 points</span>';
                    }
                } elseif (0 == $Niveau) {
                    echo '<a class="bulle" style="cursor: default;color:red;" onclick="return false;" href=""><strong>',$donnees_info['pseudo'],'</strong><span style="color:red;">Ce joueur a ton niveau</span>';
                } elseif ($score >= 50) {
                    echo '<a class="bulle" style="cursor: default;color:black;" onclick="return false;" href=""><strong>',$donnees_info['pseudo'],'</strong><span style="color:black;">Joueur trop fort</span>';
                } else {
                    echo '<a class="bulle" style="cursor: default;color:teal;" onclick="return false;" href=""><strong>',$donnees_info['pseudo'],'</strong><span style="color:teal;">Joueur ayant moins de 50 points</span>';
                }
            } else {
                echo '<a class="bulle" style="cursor: default;color:red;" onclick="return false;" href=""><strong>',$pseudo,'</strong><span style="color:red;">Tu es sur le nuage <b>'.$nuageL.'</b>, à la position <b>'.$i.'</b></span>';
            }
            echo '</a></td>';

            // Si c'est le joueur lui même : rien.
            if ($donnees_info['id'] == $id) {
                echo '<td>';
            } else {
                echo '<td>';
                $distance = abs(16 * ($nuageL - $nuageSource) + $i - $positionSource);
                // Si on a des bisous a disposition
                if (($nbE[1][0] + $nbE[1][1] + $nbE[1][2]) > 0 && 0 == $Niveau) {
                    $cout = coutAttaque($distance, $nbE[0][4]);
                    $duree = tempsAttaque($distance, $nbE[0][4]);
                    // Si on  est assez pres.
                    if ($distance <= $distMax) {
                        if (0 == $joueurBloque) {
                            echo '<a class="bulle" href="',$nuageL,'.',$i,'.action.html" >
							<img src="images/puce.png" title="" alt="" /><span>Embrasser : ',$donnees_info['pseudo'],'<br />
							Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'</span></a> ';
                        } else {
                            echo '<a class="bulle" onclick="return false;" style="cursor: default;" href="" >
							<img src="images/puce.png" title="" alt="" /><span>Embrasser : ',$donnees_info['pseudo'],'<br />
							Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'<br />
							Impossible car une action est déjà en cours</span></a> ';
                        }
                    } else {
                        echo '<a class="bulle" onclick="return false;" style="cursor: default;" href="" >
							<img src="images/puceOff.png" title="" alt="" /><span>Embrasser : ',$donnees_info['pseudo'],'<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'<br />
							Impossible car ce joueur est hors de portée</span></a> ';
                    }
                }
                if ($nbE[0][5] > 0 && 0 == $Niveau) {
                    $cout = 1000 * $distance;

                    echo '<a class="bulle" href="'.$nuageL.'.'.$i.'.yeux.html" >
					<img src="images/oeil.png" title="" alt="" /><span>Dévisager : ',$donnees_info['pseudo'],'<br />
					Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
					Distance : '.$distance.'<br />
					</span></a> ';
                }
            }
            echo '</td></tr>';
            $donnees_info = $stmt->fetch();
        } else {
            echo '<tr><td>',$i,'</td><td></td><td></td>';
            $sautPossible = 0;
            // Au moins saut niveau 1.
            if ($nbE[2][3] > 0) {
                $distance = abs(16 * ($nuageL - $nuageSource) + $i - $positionSource);
                // On prend en compte les jambes, et le niveau de saut.
                $distMax2 = distanceMax($nbE[0][4], $nbE[2][3]);
                if ($distance <= $distMax2) {
                    $sautPossible = 1;
                }
            }
            if (1 === $sautPossible) {
                $ajout = $nbE[0][0] + 0.3 * $nbE[1][0] + 0.6 * $nbE[1][1] + $nbE[1][2];
                // A modifier si on modifie calcul amour, car il est basé dessu.
                $cout = expo(20, 0.1, $ajout) * (1 + 0.1 * $distance);
                if (0 == $joueurBloque) {
                    echo '<td>
						<a class="bulle" href="',$nuageL,'.',$i,'.nuage.html" >
						<img src="images/saut.png" title="" alt="" /><span>Sauter :<br />
						Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
						Distance : '.$distance.'</span></a>
					</td></tr>';
                } else {
                    echo '<td>
						<a class="bulle" style="cursor: default;" onclick="return false;" href="" >
						<img src="images/saut.png" title="" alt="" /><span>Sauter :<br />
						Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
						Distance : '.$distance.'<br />
						Impossible car une action est déjà en cours</span></a>
					</td></tr>';
                }
            } else {
                echo '<td></td></tr>';
            }
        }
    }
    ?>
</table></center>
<?php
} else {
    echo 'T\'es pas connecté !!';
}
?>
