<?php

use Symfony\Component\Uid\Uuid;

if (isset($inMainPage) && true == $inMainPage) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();
    $castToPgTimestamptz = cast_to_pg_timestamptz();

    // ***************************************************************************
    // Gestion des attaques.
    // Phase d'aller :
    $stmt = $pdo->query(<<<'SQL'
        SELECT
            finaller,
            auteur AS sender_account_id,
            cible AS receiver_account_id
        FROM attaque
        WHERE (
            finaller <= CURRENT_TIMESTAMP
            AND state = 'EnRoute'
        )
    SQL);
    /**
     * @var array<int, array{
     *      finaller: string, // ISO 8601 timestamp string
     *      sender_account_id: string, // UUID
     *      receiver_account_id: string, // UUID
     * }> $blownKisses
     */
    $blownKisses = $stmt->fetchAll();
    foreach ($blownKisses as $blownKiss) {
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE attaque
            SET state = 'ComingBack'
            WHERE auteur = :sender_account_id
        SQL);
        $stmt->execute([
            'sender_account_id' => $blownKiss['sender_account_id'],
        ]);

        // On indique que l'attaque a eu lieu.
        $stmt = $pdo->prepare(<<<'SQL'
            INSERT INTO logatt (id, auteur, cible, timestamp)
            VALUES (:id, :sender_account_id, :receiver_account_id, :timestamp)
        SQL);
        $stmt->execute([
            'id' => Uuid::v7(),
            'sender_account_id' => $blownKiss['sender_account_id'],
            'receiver_account_id' => $blownKiss['receiver_account_id'],
            'timestamp' => $blownKiss['finaller'],
        ]);
        // Supprimer ceux vieux de plus de 12 heures.
        $stmt = $pdo->query(<<<'SQL'
            DELETE FROM logatt
            WHERE timestamp < CURRENT_TIMESTAMP - INTERVAL '12 hours'
        SQL);

        /*
        Quelques notes :
        Avantages attaquant :
            Bouche : Plus de forces pour les baisers (coefficient global mais faible)
            Apnée : prend plus de points d'amour (pourcentage)
            Flirt : attaque plus forte (coefficient global)
            Langue : baisers langoureux sont plus forts
        Avantages défenseur :
            Bouche : Plus de forces pour les baisers (coefficient global mais faible)
            Crachat : L'attaquant prend moins de points d'amour (pourcentage)
            Dents : Défense plus forte (coeff global) ET plus de chances de détruire les baisers langoureux ennemis.
            Langue : baisers langoureux sont plus forts
        */
        // Infos attaquant :
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                bouche,
                smack,
                baiser,
                pelle,
                tech1,
                tech2,
                langue,
                score
            FROM membres
            WHERE id = :sender_account_id
        SQL);
        $stmt->execute([
            'sender_account_id' => $blownKiss['sender_account_id'],
        ]);
        /**
         * @var array{
         *      bouche: int,
         *      smack: int,
         *      baiser: int,
         *      pelle: int,
         *      tech1: int,
         *      tech2: int,
         *      langue: int,
         *      score: int,
         * }|false $senderPlayer
         */
        $senderPlayer = $stmt->fetch();
        $AttSmack = $senderPlayer['smack'];
        $AttBaiser = $senderPlayer['baiser'];
        $AttPelle = $senderPlayer['pelle'];
        $AttApnee = $senderPlayer['tech1'];
        $AttFlirt = $senderPlayer['tech2'];
        $AttBouche = $senderPlayer['bouche'];
        $AttLangue = $senderPlayer['langue'];
        $AttScore = $senderPlayer['score'];

        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                coeur,
                timestamp,
                bouche,
                amour,
                smack,
                baiser,
                pelle,
                tech3,
                dent,
                langue,
                bloque,
                score
            FROM membres
            WHERE id = :receiver_account_id
        SQL);
        $stmt->execute([
            'receiver_account_id' => $blownKiss['receiver_account_id'],
        ]);
        /**
         * @var array{
         *      coeur: int,
         *      timestamp: string, // ISO 8601 timestamp string
         *      bouche: int,
         *      amour: int,
         *      smack: int,
         *      baiser: int,
         *      pelle: int,
         *      tech3: int,
         *      dent: int,
         *      langue: int,
         *      bloque: bool,
         *      score: int,
         * }|false $receiverPlayer
         */
        $receiverPlayer = $stmt->fetch();
        $DefSmack = $receiverPlayer['smack'];
        $DefBaiser = $receiverPlayer['baiser'];
        $DefPelle = $receiverPlayer['pelle'];
        $DefCrachat = $receiverPlayer['tech3'];
        $DefBouche = $receiverPlayer['bouche'];
        $DefLangue = $receiverPlayer['langue'];
        $DefDent = $receiverPlayer['dent'];
        $DefBloque = $receiverPlayer['bloque'];
        $DefScore = $receiverPlayer['score'];

        // Gestion de l'attaque (coeff * bisous):
        $AttForce = (1 + (0.1 * $AttBouche) + (0.5 * $AttFlirt)) * ($AttSmack + (2.1 * $AttBaiser) + ((3.5 + 0.2 * $AttLangue) * $AttPelle));

        $DefForce = (1 + (0.1 * $DefBouche) + (0.7 * $DefDent)) * ($DefSmack + (2.1 * $DefBaiser) + ((3.5 + 0.2 * $DefLangue) * $DefPelle));
        // Si on est déjà en attaque, on diminue considérablement la force de défense.
        if (true === $DefBloque) {
            $somme = ($DefSmack + $DefBaiser + $DefPelle);
            if (0 == $somme) {
                $somme = 1;
            }

            $DefForce /= $somme;
        }

        $bilan = $AttForce - $DefForce;
        if ($bilan < 0) {
            $AttSmack = 0;
            $AttBaiser = 0;
            $AttPelle = 0;
            $coeffBilan = $AttForce / $DefForce;
            // Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
            if (false === $DefBloque) {
                $DefSmack = floor($DefSmack * (1 - $coeffBilan / random_int(2, 10)));
                $DefBaiser = floor($DefBaiser * (1 - $coeffBilan / random_int(2, 10)));
                $DefPelle = floor($DefPelle * (1 - $coeffBilan / random_int(2, 10)));
            }

            // Attaque terminée, plus rien à voir.
            $stmt = $pdo->prepare(<<<'SQL'
                DELETE FROM attaque
                WHERE auteur = :sender_account_id
            SQL);
            $stmt->execute([
                'sender_account_id' => $blownKiss['sender_account_id'],
            ]);
            // Envoyer un MP pour signifier les résultats.
            // On supprime les unités.
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle,
                    bloque = FALSE
                WHERE id = :sender_account_id
            SQL);
            $stmt->execute([
                'smack' => $AttSmack,
                'baiser' => $AttBaiser,
                'pelle' => $AttPelle,
                'sender_account_id' => $blownKiss['sender_account_id'],
            ]);
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle
                WHERE id = :receiver_account_id
            SQL);
            $stmt->execute([
                'smack' => $DefSmack,
                'baiser' => $DefBaiser,
                'pelle' => $DefPelle,
                'receiver_account_id' => $blownKiss['receiver_account_id'],
            ]);

            AdminMP(
                $blownKiss['sender_account_id'],
                'Quel rateau !!',
                "Bouuhh, t'as perdu tout tes bisous !!\n"
                ."Tu n'as pas réussi à embrasser ton adversaire !!\n"
                ."Il te reste :\n"
                ."- 0 smacks\n"
                ."- 0 baisers\n"
                .'- 0 baisers langoureux',
            );
            AdminMP(
                $blownKiss['receiver_account_id'],
                'Bien esquivé !',
                "Bravo, tu ne t'es pas laissé faire !\n"
                ."Il te reste :\n"
                ."- {$DefSmack} smacks\n"
                ."- {$DefBaiser} baisers\n"
                ."- {$DefPelle} baisers langoureux",
            );

            // Bien se défendre fait gagner des points.
            $addScore = 5000 * ($AttScore / $DefScore);
            AjouterScore($blownKiss['receiver_account_id'], $addScore);
        } elseif (0 === $bilan) {
            $AttSmack = floor($AttSmack * (1 - 1 / random_int(2, 10)));
            $AttBaiser = floor($AttBaiser * (1 - 1 / random_int(2, 10)));
            // Gestion des dents, ca fait plutot mal...
            $dentsCoeff = $DefDent - $AttLangue;
            if ($dentsCoeff < 0) {
                $dentsCoeff = 0;
            }

            $AttPelle = floor($AttPelle * ((1 - 1 / random_int(2, 10)) * (1 - 0.1 * $dentsCoeff)));

            // Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
            if (false === $DefBloque) {
                $DefSmack = floor($DefSmack * (1 - 1 / random_int(2, 10)));
                $DefBaiser = floor($DefBaiser * (1 - 1 / random_int(2, 10)));
                $DefPelle = floor($DefPelle * (1 - 1 / random_int(2, 10)));
            }

            // Ca retourne, pas de blocage
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle
                WHERE id = :sender_account_id
            SQL);
            $stmt->execute([
                'smack' => $AttSmack,
                'baiser' => $AttBaiser,
                'pelle' => $AttPelle,
                'sender_account_id' => $blownKiss['sender_account_id'],
            ]);
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle
                WHERE id = :receiver_account_id
            SQL);
            $stmt->execute([
                'smack' => $DefSmack,
                'baiser' => $DefBaiser,
                'pelle' => $DefPelle,
                'receiver_account_id' => $blownKiss['receiver_account_id'],
            ]);

            AdminMP(
                $blownKiss['sender_account_id'],
                'Ex Aequo',
                "Egalité parfaite lors de ta dernière tentative.\n"
                ."Tu ne ramène pas de points d'amour !!\n"
                ."Il te reste :\n"
                ."- {$AttSmack} smacks\n"
                ."- {$AttBaiser} baisers\n"
                ."- {$AttPelle} baisers langoureux",
            );
            AdminMP(
                $blownKiss['receiver_account_id'],
                'Ex Aequo',
                "Egalité parfaite contre le joueur qui voulait t'embrasser.\n"
                ."Il te reste :\n"
                ."- {$DefSmack} smacks\n"
                ."- {$DefBaiser} baisers\n"
                ."- {$DefPelle} baisers langoureux",
            );
        } elseif ($bilan > 0) {
            $coeffBilan = $DefForce / $AttForce;
            $AttSmack = floor($AttSmack * (1 - $coeffBilan / random_int(2, 10)));
            $AttBaiser = floor($AttBaiser * (1 - $coeffBilan / random_int(2, 10)));
            // Gestion des dents, ca fait plutot mal...
            $dentsCoeff = $DefDent - $AttLangue;
            if ($dentsCoeff < 0) {
                $dentsCoeff = 0;
            }

            $AttPelle = floor($AttPelle * ((1 - $coeffBilan / random_int(2, 10)) * (1 - 0.1 * $dentsCoeff)));
            // Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
            if (false === $DefBloque) {
                $DefSmack = floor($DefSmack * ($coeffBilan / 2));
                $DefBaiser = floor($DefBaiser * ($coeffBilan / 2));
                $DefPelle = floor($DefPelle * ($coeffBilan / 2));
            }

            // Faire retourner, Avec butin.

            // Gestion du butin
            if (
                $blownKiss['receiver_account_id'] === $blContext['account']['id']
                && true === $blContext['is_signed_in']
            ) {
                $DefAmour = $amour;
            } else {
                $DefTimestamp = $castToUnixTimestamp->fromPgTimestamptz($receiverPlayer['timestamp']);
                $DefCoeur = $receiverPlayer['coeur'];
                $DefAmour = $receiverPlayer['amour'];
                $DefAmour = calculterAmour($DefAmour, time() - $DefTimestamp, $DefCoeur, $DefSmack, $DefBaiser, $DefPelle);
            }

            $coeffButin = 0.5 * ($AttApnee - $DefCrachat);
            if ($coeffButin < -1) {
                $coeffButin = -1;
            }

            $butin = (int) floor((1 + $coeffButin) * ($AttSmack * 100 + $AttBaiser * 1000 + $AttPelle * 10000));
            if ($butin < ($AttSmack + $AttBaiser * 10 + $AttPelle * 100)) {
                $butin = (int) ($AttSmack + $AttBaiser * 10 + $AttPelle * 100);
            }

            if ($butin > floor($DefAmour / 2)) {
                $butin = (int) floor($DefAmour / 2);
            }

            $DefAmour -= $butin;

            if (
                $blownKiss['receiver_account_id'] === $blContext['account']['id']
                && true === $blContext['is_signed_in']
            ) {
                $amour = $DefAmour;
            }

            // Ca retourne, pas de blocage
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle
                WHERE id = :sender_account_id
            SQL);
            $stmt->execute([
                'smack' => $AttSmack,
                'baiser' => $AttBaiser,
                'pelle' => $AttPelle,
                'sender_account_id' => $blownKiss['sender_account_id'],
            ]);
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET
                    amour = :amour,
                    smack = :smack,
                    baiser = :baiser,
                    pelle = :pelle
                WHERE id = :receiver_account_id
            SQL);
            $stmt->execute([
                'amour' => (int) $DefAmour,
                'smack' => $DefSmack,
                'baiser' => $DefBaiser,
                'pelle' => $DefPelle,
                'receiver_account_id' => $blownKiss['receiver_account_id'],
            ]);

            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE attaque
                SET butin = :butin
                WHERE auteur = :sender_account_id
            SQL);
            $stmt->execute([
                'butin' => $butin,
                'sender_account_id' => $blownKiss['sender_account_id'],
            ]);

            AdminMP(
                $blownKiss['sender_account_id'],
                "Tu l'as embrassé !!",
                "Bravo, tu as réussi à embrasser ton adversaire.\n"
                ."Tes bisous seront bientôt revenus près de toi.\n"
                ."Tu as réussi à prendre {$butin} Points d'Amour !!\n"
                ."Il te reste :\n"
                ."- {$AttSmack} smacks\n"
                ."- {$AttBaiser} baisers\n"
                ."- {$AttPelle} baisers langoureux",
            );
            AdminMP(
                $blownKiss['receiver_account_id'],
                "Tu t'es fait embrasser",
                "Tu n'as pas su résister à ses Bisous !!\n"
                ."Tu t'es fait prendre {$butin} Points d'Amour !!\n"
                ."Il te reste :\n"
                ."- {$DefSmack} smacks\n"
                ."- {$DefBaiser} baisers\n"
                ."- {$DefPelle} baisers langoureux",
            );

            // Bien attaquer fait gagner des points.
            $addScore = 10000 * ($DefScore / $AttScore) + ($butin / 10);
            AjouterScore($blownKiss['sender_account_id'], $addScore);
        }
    }

    // Phase retour
    $stmt = $pdo->query(<<<'SQL'
        SELECT
            auteur AS sender_account_id,
            butin
        FROM attaque
        WHERE (
            finretour <= CURRENT_TIMESTAMP
            AND state IN ('ComingBack', 'CalledOff')
        )
    SQL);
    /**
     * @var array<int, array{
     *      sender_account_id: string, // UUID
     *      butin: int,
     * }> $returningBlownKisses
     */
    $returningBlownKisses = $stmt->fetchAll();
    foreach ($returningBlownKisses as $returningBlownKiss) {
        $stmt = $pdo->prepare(<<<'SQL'
            DELETE FROM attaque
            WHERE auteur = :sender_account_id
        SQL);
        $stmt->execute([
            'sender_account_id' => $returningBlownKiss['sender_account_id'],
        ]);

        if (
            $returningBlownKiss['sender_account_id'] === $blContext['account']['id']
            && true === $blContext['is_signed_in']
        ) {
            $AttAmour = $amour;
        } else {
            $stmt = $pdo->prepare(<<<'SQL'
                SELECT amour
                FROM membres
                WHERE id = :sender_account_id
            SQL);
            $stmt->execute([
                'sender_account_id' => $returningBlownKiss['sender_account_id'],
            ]);
            /** @var array{amour: int}|false $senderPlayer */
            $senderPlayer = $stmt->fetch();
            $AttAmour = $senderPlayer['amour'];
        }

        // On fais pas de mise à jour du nb de points d'amour, pas besoin.
        // Récupération des points d'amour.
        $AttAmour += $returningBlownKiss['butin'];

        if (
            $returningBlownKiss['sender_account_id'] === $blContext['account']['id']
            && true === $blContext['is_signed_in']
        ) {
            $amour = $AttAmour;
            $joueurBloque = false;
        }

        // Libérer l'auteur et ajouter butin
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE membres
            SET
                bloque = FALSE,
                amour = :amour
            WHERE id = :sender_account_id
        SQL);
        $stmt->execute([
            'amour' => (int) $AttAmour,
            'sender_account_id' => $returningBlownKiss['sender_account_id'],
        ]);
    }
}
