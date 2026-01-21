<?php

use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;

?>
<h1>Embrasser</h1>
<?php
if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();
    $castToPgTimestamptz = cast_to_pg_timestamptz();
    if (isset($_POST['action'])) {
        $cout = 0;
        $nuageCible = $_POST['nuage'] ?? '0';
        $nuageCible = is_numeric($nuageCible) ? (int) $nuageCible : 0;
        $positionCible = $_POST['position'] ?? '0';
        $positionCible = is_numeric($positionCible) ? (int) $positionCible : 0;

        if (false === $joueurBloque) {
            if (
                (
                    $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
                    + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
                    + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]
                ) > 0
            ) {
                if (0 === $nuageCible || 0 === $positionCible) {
                    $resultat = 'Evite les valeurs nulles pour les deux champs';
                } else {
                    $stmt = $pdo->prepare(<<<'SQL'
                        SELECT
                            id,
                            nuage,
                            position,
                            score
                        FROM membres
                        WHERE (
                            nuage = :destination_nuage
                            AND position = :destination_position
                        )
                    SQL);
                    $stmt->execute([
                        'destination_nuage' => $nuageCible,
                        'destination_position' => $positionCible,
                    ]);
                    /**
                     * @var array{
                     *      id: string, // UUID
                     *      nuage: int,
                     *      position: int,
                     *      score: int,
                     * }|false $receiver
                     */
                    $receiver = $stmt->fetch();
                    if (false !== $receiver) {
                        if ($blContext['account']['id'] === $receiver['id']) {
                            $resultat = 'Il est impossible s\'attaquer soi même';
                        } else {
                            $stmt = $pdo->prepare(<<<'SQL'
                                SELECT
                                    COUNT(*) AS total_kisses_being_built
                                FROM evolution
                                WHERE (
                                    auteur = :current_account_id
                                    AND classe = 1
                                )
                            SQL);
                            $stmt->execute([
                                'current_account_id' => $blContext['account']['id'],
                            ]);
                            /** @var array{total_kisses_being_built: int}|false $result */
                            $result = $stmt->fetch();

                            // La on vérifie si le nombre est différent que zéro
                            if (
                                false !== $result
                                && 0 < $result['total_kisses_being_built']
                            ) {
                                $resultat = 'Action impossible car tu es en train de créer des Bisous';
                            } else {
                                // On détermine s'il y a une construction en cours.
                                $stmt = $pdo->prepare(<<<'SQL'
                                    SELECT COUNT(*) AS total_kisses_planned_to_be_built
                                    FROM liste
                                    WHERE (
                                        auteur = :current_account_id
                                        AND classe = 1
                                    )
                                SQL);
                                $stmt->execute([
                                    'current_account_id' => $blContext['account']['id'],
                                ]);
                                /** @var array{total_kisses_planned_to_be_built: int}|false $result */
                                $result = $stmt->fetch();
                                if (
                                    false !== $result
                                    && 0 < $result['total_kisses_planned_to_be_built']
                                ) {
                                    $resultat = 'Action impossible car tu es en train de créer des Bisous';
                                }

                                $stmt = $pdo->prepare(<<<'SQL'
                                    SELECT position, score
                                    FROM membres
                                    WHERE id = :current_account_id
                                SQL);
                                $stmt->execute([
                                    'current_account_id' => $blContext['account']['id'],
                                ]);
                                /**
                                 * @var array{
                                 *      position: int,
                                 *      score: int,
                                 * }|false $sender
                                 */
                                $sender = $stmt->fetch();

                                $receiver['score'] = floor($receiver['score'] / 1000.);
                                $sender['score'] = floor($sender['score'] / 1000.);
                                $Niveau = voirNiveau($sender['score'], $receiver['score']);

                                if (0 === $Niveau) {
                                    $distance = abs(16 * ($receiver['nuage'] - $blContext['account']['nuage']) + $receiver['position'] - $sender['position']);

                                    $distMax = distanceMax($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value], $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value]);

                                    if ($distance <= $distMax) {
                                        $cout = coutAttaque($distance, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value]);
                                        if ($amour >= $cout) {
                                            $stmt = $pdo->prepare(<<<'SQL'
                                                SELECT COUNT(*) AS total_number_of_kisses_sent_recently
                                                FROM logatt
                                                WHERE (
                                                    auteur = :current_account_id
                                                    AND cible = :receiver_account_id
                                                    AND timestamp >= CURRENT_TIMESTAMP - INTERVAL '12 hours'
                                                )
                                            SQL);
                                            $stmt->execute([
                                                'current_account_id' => $blContext['account']['id'],
                                                'receiver_account_id' => $receiver['id'],
                                            ]);
                                            /** @var array{total_number_of_kisses_sent_recently: int}|false $result */
                                            $result = $stmt->fetch();
                                            if (
                                                false !== $result
                                                && 3 > $result['total_number_of_kisses_sent_recently']
                                            ) {
                                                $amour -= $cout;
                                                $joueurBloque = true;
                                                $duree = tempsAttaque($distance, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value]);
                                                $stmt = $pdo->prepare(<<<'SQL'
                                                    UPDATE membres
                                                    SET bloque = TRUE
                                                    WHERE id = :current_account_id
                                                SQL);
                                                $stmt->execute([
                                                    'current_account_id' => $blContext['account']['id'],
                                                ]);
                                                $stmt = $pdo->prepare(<<<'SQL'
                                                    INSERT INTO attaque (auteur, cible, finaller, finretour, state)
                                                    VALUES (:current_account_id, :cible, :finaller, :finretour, 'EnRoute')
                                                SQL);
                                                $stmt->execute([
                                                    'current_account_id' => $blContext['account']['id'],
                                                    'cible' => $receiver['id'],
                                                    'finaller' => $castToPgTimestamptz->fromUnixTimestamp(time() + $duree),
                                                    'finretour' => $castToPgTimestamptz->fromUnixTimestamp(time() + 2 * $duree),
                                                ]);
                                                $estimatedTimeOfArrival = strTemps($duree);
                                                AdminMP(
                                                    $receiver['id'],
                                                    "{$blContext['account']['pseudo']} veut t'embrasser",
                                                    "{$blContext['account']['pseudo']} vient d'envoyer ses bisous dans ta direction,"
                                                    ." et va tenter de t'embrasser.\n"
                                                    ."{$blContext['account']['pseudo']} est situé sur le nuage {$blContext['account']['nuage']},"
                                                    ." à la position {$sender['position']}.\n"
                                                    ."Ses Bisous arrivent dans {$estimatedTimeOfArrival}.",
                                                );
                                                $resultat = 'Tes Bisous sont en route vers la position '
                                                    ."{$sender['position']} du nuage {$blContext['account']['nuage']},"
                                                    ." ils arriveront à destination dans {$estimatedTimeOfArrival}.";
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

        $nuageCible = $_GET['nuage'] ?? '0';
        $nuageCible = is_numeric($nuageCible) ? (int) $nuageCible : 0;
        $positionCible = $_GET['position'] ?? '0';
        $positionCible = is_numeric($positionCible) ? (int) $positionCible : 0;

        $nuageSource = $blContext['account']['nuage'];

        $stmt = $pdo->prepare(<<<'SQL'
            SELECT position
            FROM membres
            WHERE id = :current_account_id
        SQL);
        $stmt->execute([
            'current_account_id' => $blContext['account']['id'],
        ]);
        /**
         * @var array{
         *      position: int,
         * }|false $sender
         */
        $sender = $stmt->fetch();

        $distance = abs(16 * ($nuageCible - $blContext['account']['nuage']) + $positionCible - $sender['position']);

        $cout = coutAttaque($distance, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value]);
    } else {
        $nuageCible = 0;
        $positionCible = 0;
        $cout = 0;
    }
    if (isset($resultat)) {
        echo '<span class="info">[ '.$resultat.' ]</span><br /><br />';
    }
    ?>

    <?php if (false === $joueurBloque) { ?>
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

    <?php } else { ?>
    Tes Bisous sont en chemin.<br />
    <?php } ?>

<?php } else { ?>
    Tu n'es pas connecté !!
<?php } ?>
