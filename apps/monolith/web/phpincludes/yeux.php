<?php if (true == $_SESSION['logged']) {
    $pdo = bd_connect();

    if (isset($_GET['Dnuage'], $_GET['Dpos']) && !empty($_GET['Dnuage']) && !empty($_GET['Dpos'])) {
        $Dnuage = htmlentities((string) $_GET['Dnuage']);
        $Dpos = htmlentities((string) $_GET['Dpos']);
        if ($nbE[0][5] > 0) {
            $stmt = $pdo->prepare('SELECT id, oeil, score, pseudo FROM membres WHERE nuage = :nuage AND position = :position');
            $stmt->execute(['nuage' => $Dnuage, 'position' => $Dpos]);
            if ($donnees = $stmt->fetch()) {
                $Did = $donnees['id'];
                $Doeil = $donnees['oeil'];
                $scoreCible = $donnees['score'];
                $pseudoCible = $donnees['pseudo'];

                $stmt = $pdo->prepare('SELECT score, position, nuage, espion, oeil FROM membres WHERE id = :id');
                $stmt->execute(['id' => $id]);
                $donnees = $stmt->fetch();
                $scoreSource = $donnees['score'];
                $positionSource = $donnees['position'];
                $nuageSource = $donnees['nuage'];
                $oeilSource = $donnees['oeil'];
                $espionSource = $donnees['espion'];

                $scoreCible = floor($scoreCible / 1000.);
                $scoreSource = floor($scoreSource / 1000.);
                $Niveau = voirNiveau($scoreSource, $scoreCible);
                if (0 == $Niveau) {
                    $distance = abs(16 * ($Dnuage - $nuageSource) + $Dpos - $positionSource);
                    $cout = 1000 * $distance;
                    if ($amour >= $cout) {
                        $amour -= $cout;
                        $max = $oeilSource - $Doeil;
                        if ($max < 0) {
                            $max = 0;
                        }
                        $lvlInfo = random_int(0, $max);

                        AdminMP($Did, "{$pseudo} t'a dévisagé", $pseudo." vient de te dévisager, et cherche peut-être à t'embrasser.");

                        $resultat = "Tu as dévisagé {$pseudoCible}";

                        // Mise à jour des PA de l'espionné :

                        // Note :
                        // coeur, bouche, amour, jambes, smack, baiser, pelle, tech1, tech2, tech3, tech4, dent, langue, bloque, soupe, oeil
                        switch ($lvlInfo) {
                            case 0:
                                $resDev = 'Degré d\'information : '.$lvlInfo.'/'.$max.'

							Malheureusement, tu n\'as pu obtenir aucune information sur '.$pseudoCible.'
							';
                                break;
                            default:
                                $stmt = $pdo->prepare('SELECT amour, timestamp, oeil, smack, baiser, pelle, coeur FROM membres WHERE id = :id');
                                $stmt->execute(['id' => $Did]);
                                $donnees = $stmt->fetch();
                        }

                        if ($lvlInfo >= 1) {
                            $DefAmour = calculterAmour($donnees['amour'], time() - $donnees['timestamp'], $donnees['coeur'], $donnees['smack'], $donnees['baiser'], $donnees['pelle']);

                            $resDev = 'Degré d\'information : '.$lvlInfo.'/'.$max.'

							'.$pseudoCible.' dispose de :

							'.formaterNombre(floor($DefAmour)).' Points d\'Amour

							';
                        }
                        if ($lvlInfo >= 2) {
                            $resDev .= 'Un oeil niveau '.$donnees['oeil'].'

							';
                        }
                        if ($lvlInfo >= 3) {
                            $resDev .= $donnees['smack'].' Smack'.pluriel($donnees['smack']).'

							';
                        }
                        if ($lvlInfo >= 4) {
                            $resDev .= $donnees['baiser'].' Baiser'.pluriel($donnees['baiser']).'

							';
                        }
                        if ($lvlInfo >= 5) {
                            $resDev .= $donnees['pelle'].' Baiser'.pluriel($donnees['pelle']).' langoureux

							';
                        }

                        // Envoyer un MP si le user le désire.
                        if (1 == $espionSource && 0 != $lvlInfo) {
                            AdminMP($id, "Tu as dévisagé {$pseudoCible}", $resDev, 1);
                        }
                    } else {
                        $resultat = "Tu n'as pas assez de Points d'Amour";
                    }
                } else {
                    $resultat = "Tu n'as pas le même niveau que ce joueur";
                }
            } else {
                $resultat = "Il n'y a plus de joueur a cette position";
            }
        } else {
            $resultat = 'Il te faut des yeux niveau 1 pour dévisager un joueur';
        }
        ?>
<h1>Dévisager</h1>
<br />
<a href="<?php echo $Dnuage; ?>.nuage.html">Retourner sur le nuage en cours</a><br />
<br />
<?php
            if (isset($resultat)) {
                echo '<span class="info">[ '.$resultat.' ]</span><br /><br />';
            }
        if (isset($resDev)) {
            echo nl2br(htmlentities($resDev));
            if (0 != $lvlInfo) {
                if (1 == $espionSource) {
                    echo "Un message t'a été envoyé pour enregistrer ces informations.<br />";
                } else {
                    echo 'Va dans Mon compte si tu désires sauvegarder ces informations dans des messages.<br />';
                }
            }
            if ($amour >= $cout) {
                echo '<a href="'.$Dnuage.'.'.$Dpos.'.yeux.html">Dévisager '.$pseudoCible.' de nouveau (nécessite '.$cout.' Points d\'Amour)</a>';
            }
        }
    } else {
        echo 'Page inaccessible.';
    }
} else {
    echo 'Tu n\'es pas connecté !!';
}
