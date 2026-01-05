<?php

use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;
use Bl\Domain\Upgradable\UpgradableTechnique;

?>
<h1>Nuages</h1>
<?php
if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();

    // Infos sur le joueur.
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            position AS cloud_coordinates_y,
            score
        FROM membres
        WHERE id = :current_account_id
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /**
     * @var array{
     *     cloud_coordinates_y: int,
     *     score: int,
     * }|false $currentPlayer
     */
    $currentPlayer = $stmt->fetch();
    if (false === $currentPlayer) {
        throw new RuntimeException('Current player not found');
    }
    $scoreSource = floor($currentPlayer['score'] / 1000.);

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            nombre AS total_clouds
        FROM nuage
        WHERE id = :cloud_config_id
    SQL);
    $stmt->execute([
        'cloud_config_id' => '00000000-0000-0000-0000-000000000002',
    ]);
    /**
     * @var array{
     *     total_clouds: int,
     * }|false $cloudConfig
     */
    $cloudConfig = $stmt->fetch();
    if (false === $cloudConfig) {
        throw new RuntimeException('Cloud config not found');
    }

    if (isset($_POST['nuage']) && !empty($_POST['nuage'])) {
        $currentCloud = (int) $_POST['nuage'];
        if ($currentCloud < 1) {
            $currentCloud = 1;
        }
        if ($currentCloud > $cloudConfig['total_clouds']) {
            $currentCloud = $cloudConfig['total_clouds'];
        }
    } elseif (isset($_GET['nuage']) && !empty($_GET['nuage'])) {
        $currentCloud = (int) $_GET['nuage'];
        if ($currentCloud < 1) {
            $currentCloud = 1;
        }
        if ($currentCloud > $cloudConfig['total_clouds']) {
            $currentCloud = $cloudConfig['total_clouds'];
        }
    } elseif (isset($_GET['saut']) && !empty($_GET['saut']) && isset($_GET['sautnuage']) && !empty($_GET['sautnuage'])) {
        $currentCloud = (int) $_GET['sautnuage'];
        if ($currentCloud > 0) {
            if ($currentCloud <= $cloudConfig['total_clouds']) {
                if (isset($_GET['sautposition']) && !empty($_GET['sautposition'])) {
                    $receiverCoordinatesY = (int) $_GET['sautposition'];

                    if ($receiverCoordinatesY > 0 && $receiverCoordinatesY < 17) {
                        // Au moins saut niveau 1.
                        if ($currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value] > 0) {
                            $distance = abs(
                                16 * ($currentCloud - $blContext['account']['nuage'])
                                + $receiverCoordinatesY
                                - $currentPlayer['cloud_coordinates_y'],
                            );
                            // On prend en compte les jambes, et le niveau de saut.
                            $distMax = distanceMax($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value], $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value]);
                            if ($distance <= $distMax) {
                                // Vérifions si il ya quelqu'un :
                                $stmt = $pdo->prepare(<<<'SQL'
                                    SELECT id
                                    FROM membres
                                    WHERE (
                                        nuage = :destination_nuage
                                        AND position = :destination_position
                                    )
                                SQL);
                                $stmt->execute([
                                    'destination_nuage' => $currentCloud,
                                    'destination_position' => $receiverCoordinatesY,
                                ]);
                                /**
                                 * @var array{
                                 *     id: string, // UUID
                                 * }|false $playerAtPosition
                                 */
                                $playerAtPosition = $stmt->fetch();
                                if (false !== $playerAtPosition) {
                                    $resultat = 'La position est déjà occupée';
                                } elseif (false === $joueurBloque) {
                                    $stmt = $pdo->prepare(<<<'SQL'
                                        SELECT auteur
                                        FROM attaque
                                        WHERE (
                                            cible = :current_account_id
                                            AND state = 'EnRoute'
                                        )
                                    SQL);
                                    $stmt->execute([
                                        'current_account_id' => $blContext['account']['id'],
                                    ]);
                                    /**
                                     * @var array{
                                     *     auteur: string, // UUID
                                     * }|false $incomingKiss
                                     */
                                    $incomingKiss = $stmt->fetch();
                                    if (false !== $incomingKiss) {
                                        $resultat = "Tu ne peux pas sauter car quelqu'un tente de t'embrasser";
                                    } else {
                                        $ajout = $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value]
                                            + 0.3 * $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
                                            + 0.6 * $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
                                            + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value];
                                        // A modifier si on modifie calcul amour, car il est basé dessu.
                                        $cout = expo(20, 0.1, $ajout) * (1 + 0.1 * $distance);
                                        if ($amour >= $cout) {
                                            $amour -= $cout;
                                            $stmt = $pdo->prepare(<<<'SQL'
                                                UPDATE membres
                                                SET
                                                    nuage = :destination_nuage,
                                                    position = :destination_position
                                                WHERE id = :current_account_id
                                            SQL);
                                            $stmt->execute([
                                                'destination_nuage' => $currentCloud,
                                                'destination_position' => $receiverCoordinatesY,
                                                'current_account_id' => $blContext['account']['id'],
                                            ]);
                                            $blContext['account']['nuage'] = $currentCloud;
                                            $blContext['account']['nuage'] = $currentCloud;
                                            $currentPlayer['cloud_coordinates_y'] = $receiverCoordinatesY;
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
                $currentCloud = $cloudConfig['total_clouds'];
                $resultat = 'Il n\'existe pas de nuage supérieur à '.$cloudConfig['total_clouds'];
            }
        } else {
            $currentCloud = 1;
            $resultat = 'Il n\'existe pas de nuage inférieur à 1';
        }
    } else {
        $currentCloud = $blContext['account']['nuage'];
    }

    $nextCloud = $currentCloud + 1;
    if ($nextCloud > $cloudConfig['total_clouds']) {
        $nextCloud = $cloudConfig['total_clouds'];
    }
    $previousCloud = $currentCloud - 1;
    if ($previousCloud < 1) {
        $previousCloud = 1;
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
				<input type="hidden" name="nuage" value="<?php echo $previousCloud; ?>" />
				<input type="submit" name="bouton" value="&lt;-" />
				</form>
			</td>
			<td>
				<form method="post" action="nuage.html">
				<input type="text" name="nuage" maxlength="3" value="<?php echo $currentCloud; ?>" tabindex="50" size="2"/>
				<input type="submit" name="bouton" value="Voir" />
				</form>
			</td>
			<td>
				<form method="post" action="nuage.html">
				<input type="hidden" name="nuage" value="<?php echo $nextCloud; ?>" />
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
} elseif (true === $joueurBloque) {
    echo '<span class="info">[ Rappel : Tu es train de tenter d\'embrasser un joueur ]</span><br /><br />';
} elseif (($currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value] + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value] + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]) === 0) {
    echo '<span class="info">[ Rappel : Tu n\'as pas de Bisous pour embrasser]</span><br /><br />';
}
    if (0 === $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value]) {
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

    $distMax = distanceMax(
        $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value],
        $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value],
    );

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            id,
            pseudo,
            position,
            lastconnect,
            score
        FROM membres
        WHERE nuage = :cloud_coordinate_x
        ORDER BY position ASC
    SQL);
    $stmt->execute([
        'cloud_coordinate_x' => $currentCloud,
    ]);
    /**
     * @var array<int, array{
     *     id: string, // UUID
     *     pseudo: string,
     *     position: int,
     *     lastconnect: string, // ISO 8601 timestamp string
     *     score: int,
     * }> $playersByPosition
     */
    $playersByPosition = [];
    foreach ($stmt->fetchAll() as $row) {
        $playersByPosition[$row['position']] = $row;
    }
    for ($i = 1; $i <= 16; ++$i) {
        if (isset($playersByPosition[$i])) {
            $player = $playersByPosition[$i];
            $player['pseudo'] = stripslashes((string) $player['pseudo']);
            echo '<tr><td>',$i,'</td><td>';
            if ($castToUnixTimestamp->fromPgTimestamptz($player['lastconnect']) > time() - 300) {
                echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>'.$player['pseudo'].' est connect&eacute;</span></a> ';
            } else {
                echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>'.$player['pseudo']." n'est pas connect&eacute;</span></a> ";
            }
            echo '</td><td>';
            if ($player['id'] !== $blContext['account']['id']) {
                $score = floor($player['score'] / 1000.);
                $Niveau = voirNiveau($scoreSource, $score);
                if (1 === $Niveau) {
                    if ($score >= 50) {
                        echo '<a class="bulle" style="cursor: default;color:blue;" onclick="return false;" href=""><strong>',$player['pseudo'],'</strong><span style="color:blue;">Joueur trop faible</span>';
                    } else {
                        echo '<a class="bulle" style="cursor: default;color:teal;" onclick="return false;" href=""><strong>',$player['pseudo'],'</strong><span style="color:teal;">Joueur ayant moins de 50 points</span>';
                    }
                } elseif (0 === $Niveau) {
                    echo '<a class="bulle" style="cursor: default;color:red;" onclick="return false;" href=""><strong>',$player['pseudo'],'</strong><span style="color:red;">Ce joueur a ton niveau</span>';
                } elseif ($score >= 50) {
                    echo '<a class="bulle" style="cursor: default;color:black;" onclick="return false;" href=""><strong>',$player['pseudo'],'</strong><span style="color:black;">Joueur trop fort</span>';
                } else {
                    echo '<a class="bulle" style="cursor: default;color:teal;" onclick="return false;" href=""><strong>',$player['pseudo'],'</strong><span style="color:teal;">Joueur ayant moins de 50 points</span>';
                }
            } else {
                echo '<a class="bulle" style="cursor: default;color:red;" onclick="return false;" href=""><strong>',$blContext['account']['pseudo'],'</strong><span style="color:red;">Tu es sur le nuage <b>'.$currentCloud.'</b>, à la position <b>'.$i.'</b></span>';
            }
            echo '</a></td>';

            // Si c'est le joueur lui même : rien.
            if ($player['id'] === $blContext['account']['id']) {
                echo '<td>';
            } else {
                echo '<td>';
                $distance = abs(16 * ($currentCloud - $blContext['account']['nuage']) + $i - $currentPlayer['cloud_coordinates_y']);
                // Si on a des bisous a disposition
                if (
                    (
                        $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
                        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
                        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]
                    ) > 0
                    && 0 === $Niveau
                ) {
                    $cout = coutAttaque(
                        $distance,
                        $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value],
                    );
                    $duree = tempsAttaque(
                        $distance,
                        $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value],
                    );
                    // Si on  est assez pres.
                    if ($distance <= $distMax) {
                        if (false === $joueurBloque) {
                            echo '<a class="bulle" href="',$currentCloud,'.',$i,'.action.html" >
							<img src="images/puce.png" title="" alt="" /><span>Embrasser : ',$player['pseudo'],'<br />
							Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'</span></a> ';
                        } else {
                            echo '<a class="bulle" onclick="return false;" style="cursor: default;" href="" >
							<img src="images/puce.png" title="" alt="" /><span>Embrasser : ',$player['pseudo'],'<br />
							Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'<br />
							Impossible car une action est déjà en cours</span></a> ';
                        }
                    } else {
                        echo '<a class="bulle" onclick="return false;" style="cursor: default;" href="" >
							<img src="images/puceOff.png" title="" alt="" /><span>Embrasser : ',$player['pseudo'],'<br />
							Distance : '.$distance.'<br />
							Durée : '.strTemps($duree).'<br />
							Impossible car ce joueur est hors de portée</span></a> ';
                    }
                }
                if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Eyes->value] > 0 && 0 === $Niveau) {
                    $cout = 1000 * $distance;

                    echo '<a class="bulle" href="'.$currentCloud.'.'.$i.'.yeux.html" >
					<img src="images/oeil.png" title="" alt="" /><span>Dévisager : ',$player['pseudo'],'<br />
					Nécessite '.formaterNombre(ceil($cout)).' Points d\'Amour<br />
					Distance : '.$distance.'<br />
					</span></a> ';
                }
            }
            echo '</td></tr>';
        } else {
            echo '<tr><td>',$i,'</td><td></td><td></td>';
            $sautPossible = 0;
            // Au moins saut niveau 1.
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value] > 0) {
                $distance = abs(16 * ($currentCloud - $blContext['account']['nuage']) + $i - $currentPlayer['cloud_coordinates_y']);
                // On prend en compte les jambes, et le niveau de saut.
                $distMax2 = distanceMax(
                    $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value],
                    $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value],
                );
                if ($distance <= $distMax2) {
                    $sautPossible = 1;
                }
            }
            if (1 === $sautPossible) {
                $ajout = $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value]
                    + 0.3 * $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
                    + 0.6 * $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
                    + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value];
                // A modifier si on modifie calcul amour, car il est basé dessu.
                $cout = expo(20, 0.1, $ajout) * (1 + 0.1 * $distance);
                if (false === $joueurBloque) {
                    echo '<td>
						<a class="bulle" href="',$currentCloud,'.',$i,'.nuage.html" >
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
