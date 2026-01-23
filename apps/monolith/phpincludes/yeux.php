<?php

use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;

if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();

    if (isset($_GET['Dnuage'], $_GET['Dpos']) && !empty($_GET['Dnuage']) && !empty($_GET['Dpos'])) {
        if (0 < $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Eyes->value]) {
            $stmt = $pdo->prepare(<<<'SQL'
                SELECT
                    id,
                    pseudo,
                    nuage,
                    position,
                    score,
                    oeil,
                    amour,
                    timestamp,
                    smack,
                    baiser,
                    pelle,
                    coeur
                FROM membres
                WHERE (
                    nuage = :destination_nuage
                    AND position = :destination_position
                )
            SQL);
            $stmt->execute([
                'destination_nuage' => $_GET['Dnuage'],
                'destination_position' => $_GET['Dpos'],
            ]);
            /**
             * @var array{
             *     id: string, // UUID
             *     pseudo: string,
             *     nuage: int,
             *     position: int,
             *     score: int,
             *     oeil: int,
             *     amour: int,
             *     timestamp: string, // ISO 8601 timestamp string
             *     smack: int,
             *     baiser: int,
             *     pelle: int,
             *     coeur: int,
             * }|false $receiver
             */
            $receiver = $stmt->fetch();
            if (false !== $receiver) {
                $stmt = $pdo->prepare(<<<'SQL'
                    SELECT
                        score,
                        position,
                        nuage,
                        oeil
                    FROM membres
                    WHERE id = :current_account_id
                SQL);
                $stmt->execute([
                    'current_account_id' => $blContext['account']['id'],
                ]);
                /**
                 * @var array{
                 *     score: int,
                 *     position: int,
                 *     nuage: int,
                 *     oeil: int,
                 * }|false $currentPlayer
                 */
                $currentPlayer = $stmt->fetch();
                $scoreSource = $currentPlayer['score'];

                $receiver['score'] = floor($receiver['score'] / 1000.);
                $scoreSource = floor($scoreSource / 1000.);
                $Niveau = voirNiveau($scoreSource, $receiver['score']);
                if (0 === $Niveau) {
                    $distance = abs(
                        16 * (
                            $receiver['nuage']
                            - $currentPlayer['nuage']
                        )
                        + $receiver['position']
                        - $currentPlayer['position'],
                    );
                    $cout = 1000 * $distance;
                    if ($cout <= $amour) {
                        $amour -= $cout;
                        $max = $currentPlayer['oeil'] - $receiver['oeil'];
                        if (0 > $max) {
                            $max = 0;
                        }
                        $lvlInfo = random_int(0, $max);

                        sendNotification(
                            $receiver['id'],
                            'Tu as été dévisagé',
                            <<<TXT
                            {$blContext['account']['pseudo']} vient de te dévisager, et cherche peut-être à t'embrasser.
                            TXT,
                        );

                        $resultat = "Tu as dévisagé {$receiver['pseudo']}";

                        // Mise à jour des PA de l'espionné :

                        // Note :
                        // coeur, bouche, amour, jambes, smack, baiser, pelle, tech1, tech2, tech3, tech4, dent, langue, bloque, soupe, oeil
                        if (0 === $lvlInfo) {
                            $resDev = <<<TXT
                            Degré d'information : {$lvlInfo}/{$max}

                            Malheureusement, tu n'as pu obtenir aucune information sur {$receiver['pseudo']}
                            TXT;
                        }
                        if (1 <= $lvlInfo) {
                            $DefAmour = formaterNombre(floor(calculterAmour(
                                $receiver['amour'],
                                time() - $castToUnixTimestamp->fromPgTimestamptz($receiver['timestamp']),
                                $receiver['coeur'],
                                $receiver['smack'],
                                $receiver['baiser'],
                                $receiver['pelle'],
                            )));

                            $resDev = <<<TXT
                            Degré d'information : {$lvlInfo}/{$max}

                            {$receiver['pseudo']} dispose de :

                            {$DefAmour} Points d'Amour

                            TXT;
                        }
                        if (2 <= $lvlInfo) {
                            $resDev .= "Un oeil niveau {$receiver['oeil']}\n\n";
                        }
                        if (3 <= $lvlInfo) {
                            $resDev .= "Smacks : {$receiver['smack']}\n\n";
                        }
                        if (4 <= $lvlInfo) {
                            $resDev .= "Baisers : {$receiver['baiser']}\n\n";
                        }
                        if (5 <= $lvlInfo) {
                            $resDev .= "Baisers Langoureux : {$receiver['pelle']}\n\n";
                        }

                        // Envoyer un MP si le user le désire.
                        if (0 !== $lvlInfo) {
                            sendNotification(
                                $blContext['account']['id'],
                                'Tu as dévisagé ton crush !',
                                $resDev,
                            );
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
<a href="<?php echo htmlspecialchars($_GET['Dnuage']); ?>.nuage.html">Retourner sur le nuage en cours</a><br />
<br />
<?php
            echo '<span class="info">[ '.$resultat.' ]</span><br /><br />';
        if (isset($resDev)) {
            echo nl2br(htmlentities($resDev));
            if (0 != $lvlInfo) {
                echo "Un message t'a été envoyé pour enregistrer ces informations.<br />";
            }
            if ($cout <= $amour) {
                echo '<a href="'.$receiver['nuage'].'.'.$receiver['position'].'.yeux.html">Dévisager '.$receiver['pseudo'].' de nouveau (nécessite '.$cout." Points d'Amour)</a>";
            }
        }
    } else {
        echo 'Page inaccessible.';
    }
} else {
    echo 'Tu n\'es pas connecté !!';
}
