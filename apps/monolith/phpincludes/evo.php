<?php

use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;
use Bl\Domain\Upgradable\UpgradableTechnique;
use Symfony\Component\Uid\Uuid;

/**
 * @param array<int, mixed> $currentPlayerUpgradableLevels
 */
function arbre(int $classe, int $type, array $currentPlayerUpgradableLevels): bool
{
    if (UpgradableCategory::Organs->value === $classe) {
        if (UpgradableOrgan::Heart->value === $type) {
            // coeur
            return true;
        }

        if (UpgradableOrgan::Mouth->value === $type) {
            // bouche
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 2) {
                return true;
            }
        } elseif (UpgradableOrgan::Tongue->value === $type) {
            // langue
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 2
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 5
            ) {
                return true;
            }
        } elseif (UpgradableOrgan::Teeth->value === $type) {
            // dent
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 2) {
                return true;
            }
        } elseif (UpgradableOrgan::Legs->value === $type) {
            // jambes
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 15) {
                return true;
            }
        } elseif (UpgradableOrgan::Eyes->value === $type) {
            // oeil
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 10) {
                return true;
            }
        }
    } elseif (UpgradableCategory::Bisous->value === $classe) {
        if (UpgradableBisou::Peck->value === $type) {
            // smack
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 2) {
                return true;
            }
        } elseif (UpgradableBisou::Smooch->value === $type) {
            // baiser
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 6) {
                return true;
            }
        } elseif (UpgradableBisou::FrenchKiss->value === $type) {
            // baiser langoureux
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Tongue->value] >= 5
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 10
            ) {
                return true;
            }
        }
    } elseif (UpgradableCategory::Techniques->value === $classe) {
        if (UpgradableTechnique::HoldBreath->value === $type) {
            // Apnée
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 3
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 2
            ) {
                return true;
            }
        } elseif (UpgradableTechnique::Flirt->value === $type) {
            // Flirt
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 5
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 4
            ) {
                return true;
            }
        } elseif (UpgradableTechnique::Spit->value === $type) {
            // Crachat
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::HoldBreath->value] >= 1
                && $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Flirt->value] >= 3
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Tongue->value] >= 3
            ) {
                return true;
            }
        } elseif (UpgradableTechnique::Leap->value === $type) {
            // Saut
            if ($currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value] >= 2) {
                return true;
            }
        } elseif (UpgradableTechnique::Soup->value === $type) {
            // Soupe
            if (
                $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] >= 15
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] >= 8
                && $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Tongue->value] >= 4
            ) {
                return true;
            }
        }
    }

    return false;
}

if (isset($inMainPage) && true === $inMainPage) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();
    $castToPgTimestamptz = cast_to_pg_timestamptz();

    // Nombre de type différents pour la classe concernée.
    $upgradableTypeCount = count(UpgradableCategory::from($evolPage)->getCases());
    $evolution = -1; // Valeur par défaut ( = aucune construction en cours).

    // Annuler une construction ne permet pas de récupérer les points.
    if (isset($_POST['cancel']) || isset($_GET['cancel'])) {
        $classeCancel = $evolPage;
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT cout
            FROM evolution
            WHERE (
                auteur = :current_account_id
                AND classe = :classe
            )
        SQL);
        $stmt->execute([
            'current_account_id' => $blContext['account']['id'],
            'classe' => $classeCancel,
        ]);
        /**
         * @var array{
         *     cout: int,
         * }|false $cancelledEvolution
         */
        $cancelledEvolution = $stmt->fetch();
        if (false !== $cancelledEvolution) {
            $amour += (int) ($cancelledEvolution['cout'] / 2);
        }

        $stmt = $pdo->prepare(<<<'SQL'
            DELETE FROM evolution
            WHERE (
                auteur = :current_account_id
                AND classe = :classe
            )
        SQL);
        $stmt->execute([
            'current_account_id' => $blContext['account']['id'],
            'classe' => $classeCancel,
        ]);

        // On passe à une nouvelle construction si disponible.
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                id,
                duree,
                type,
                cout
            FROM liste
            WHERE (
                auteur = :current_account_id
                AND classe = :classe
            )
            ORDER BY id
            LIMIT 1 OFFSET 0
        SQL);
        $stmt->execute([
            'current_account_id' => $blContext['account']['id'],
            'classe' => $classeCancel,
        ]);
        /**
         * @var array{
         *     id: string, // UUID
         *     duree: int,
         *     type: int,
         *     cout: int,
         * }|false $nextQueuedItem
         */
        $nextQueuedItem = $stmt->fetch();
        if (false !== $nextQueuedItem) {
            $timeFin2 = time() + $nextQueuedItem['duree'];
            $stmt2 = $pdo->prepare(<<<'SQL'
                INSERT INTO evolution (id, timestamp, classe, type, auteur, cout)
                VALUES (:id, :timestamp, :classe, :type, :current_account_id, :cout)
            SQL);
            $stmt2->execute([
                'id' => Uuid::v7(),
                'timestamp' => $castToPgTimestamptz->fromUnixTimestamp($timeFin2),
                'classe' => $classeCancel,
                'type' => $nextQueuedItem['type'],
                'current_account_id' => $blContext['account']['id'],
                'cout' => $nextQueuedItem['cout'],
            ]);
            $stmt2 = $pdo->prepare(<<<'SQL'
                DELETE FROM liste
                WHERE id = :queued_item_id
            SQL);
            $stmt2->execute([
                'queued_item_id' => $nextQueuedItem['id'],
            ]);

            if (UpgradableCategory::Bisous->value === $classeCancel) {
                // $amour -= $nextQueuedItem['cout'];
            }
        }
    }

    // On détermine s'il y a une construction en cours.
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT COUNT(*) AS total_evolutions_in_progress
        FROM evolution
        WHERE (
            auteur = :current_account_id
            AND classe = :classe
        )
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
        'classe' => $evolPage,
    ]);
    /** @var array{total_evolutions_in_progress: int}|false $countResult */
    $countResult = $stmt->fetch();
    if (
        false !== $countResult
        && 0 < $countResult['total_evolutions_in_progress']
    ) {
        // Si oui, on récupère les infos sur la construction.
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                timestamp,
                type
            FROM evolution
            WHERE (
                auteur = :current_account_id
                AND classe = :classe
            )
        SQL);
        $stmt->execute([
            'current_account_id' => $blContext['account']['id'],
            'classe' => $evolPage,
        ]);
        /**
         * @var array{
         *     timestamp: string, // ISO 8601 timestamp string
         *     type: int,
         * }|false $currentEvolution
         */
        $currentEvolution = $stmt->fetch();
        if (false !== $currentEvolution) {
            // Date a laquelle la construction sera terminée.
            $timeFin = $castToUnixTimestamp->fromPgTimestamptz($currentEvolution['timestamp']);
            // Type de la construction.
            $evolution = $currentEvolution['type'];
        }

        // partie qui permet d'ajouter des constructions si il ya déjà des constructions en cours.
        $i = 0;
        $stop = 0;
        if (true === $joueurBloque && UpgradableCategory::Bisous->value === $evolPage) {
            $stop = 1;
        }

        while ($i !== $upgradableTypeCount && 0 === $stop) {
            // Pour l'instant, on gère ca que pour les bisous.
            $upgradableItem = UpgradableCategory::from($evolPage)->getType($i)->toString();
            if (
                isset($_POST[$upgradableItem])
                && UpgradableCategory::Bisous->value === $evolPage
                && (
                    $amour >= $amourE[$evolPage][$i]
                    && arbre($evolPage, $i, $currentPlayerUpgradableLevels)
                )
            ) {
                $stmt = $pdo->prepare(<<<'SQL'
                    SELECT COUNT(*) AS total_queued_items
                    FROM liste
                    WHERE (
                        auteur = :current_account_id
                        AND classe = 1
                    )
                SQL);
                $stmt->execute([
                    'current_account_id' => $blContext['account']['id'],
                ]);
                /** @var array{total_queued_items: int}|false $queueCountResult */
                $queueCountResult = $stmt->fetch();
                if (
                    false !== $queueCountResult
                    && 9 > $queueCountResult['total_queued_items']
                ) {
                    // Construction demandée, donc on arrete la boucle.
                    $stop = 1;
                    $dureeConst = $tempsE[$evolPage][$i];
                    $stmt2 = $pdo->prepare(<<<'SQL'
                        INSERT INTO liste (id, duree, classe, type, auteur, cout)
                        VALUES (:id, :duree, :classe, :type, :current_account_id, :cout)
                    SQL);
                    $stmt2->execute([
                        'id' => Uuid::v7(),
                        'duree' => $dureeConst,
                        'classe' => $evolPage,
                        'type' => $i,
                        'current_account_id' => $blContext['account']['id'],
                        'cout' => $amourE[$evolPage][$i],
                    ]);
                    // On décrémente le nombre de points d'amour.
                    $amour -= $amourE[$evolPage][$i];
                }
            }

            ++$i;
        }
    } else {
        // Si rien n'est en construction, on peut construire.
        $i = 0;
        $stop = 0;
        // On va vérifier pour chaque type d'objet si il ya une demande de construction.
        // Une fois une demande trouvée, on arrete la boucle.
        // Si on est sur la page de construction des Bisous et on attaque, pas de construction possible.
        if (true === $joueurBloque && UpgradableCategory::Bisous->value === $evolPage) {
            $stop = 1;
        }

        while ($i !== $upgradableTypeCount && 0 === $stop) {
            // On regarde si on a demandé la construction, et si on a le nombre de points d'amour nécessaire.
            // (La vérification du nombre de points d'amour permet d'éviter les tricheurs --> sécurité)
            $upgradableItem = UpgradableCategory::from($evolPage)->getType($i)->toString();
            if (
                isset($_POST[$upgradableItem])
                && $amour >= $amourE[$evolPage][$i]
                && arbre($evolPage, $i, $currentPlayerUpgradableLevels)
            ) {
                // Construction demandée, donc on arrete la boucle.
                $stop = 1;
                // On calcule la date de fin du calcul (servira aussi pour l'affichage sur la page).
                $timeFin = time() + $tempsE[$evolPage][$i];
                // On met l'objet en construction.
                $stmt = $pdo->prepare(<<<'SQL'
                    INSERT INTO evolution (id, timestamp, classe, type, auteur, cout)
                    VALUES (:id, :timestamp, :classe, :type, :current_account_id, :cout)
                SQL);
                $stmt->execute([
                    'id' => Uuid::v7(),
                    'timestamp' => $castToPgTimestamptz->fromUnixTimestamp($timeFin),
                    'classe' => $evolPage,
                    'type' => $i,
                    'current_account_id' => $blContext['account']['id'],
                    'cout' => $amourE[$evolPage][$i],
                ]);
                // On décrémente le nombre de points d'amour.
                $amour -= $amourE[$evolPage][$i];
                // On indique le type du batiment en construction, pour l'affichage sur la page.
                $evolution = $i;
            }

            // Incrémentation de la boucle.
            ++$i;
        }
    }
}
