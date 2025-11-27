<?php

use Symfony\Component\Uid\Uuid;

if (isset($inMainPage) && true == $inMainPage) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();
    $castToPgTimestamptz = cast_to_pg_timestamptz();

    // ***************************************************************************
    // Gestion des attaques.
    // Phase d'aller :
    $sql_info = $pdo->query("SELECT finaller, auteur, cible FROM attaque WHERE finaller <= CURRENT_TIMESTAMP AND state = 'EnRoute'");
    while ($donnees_info = $sql_info->fetch()) {
        $idAuteur = $donnees_info['auteur'];
        $idCible = $donnees_info['cible'];
        $finaller = $donnees_info['finaller'];
        $stmt = $pdo->prepare("UPDATE attaque SET state = 'ComingBack' WHERE auteur = :auteur");
        $stmt->execute(['auteur' => $idAuteur]);

        // On indique que l'attaque a eu lieu.
        $stmt = $pdo->prepare('INSERT INTO logatt (id, auteur, cible, timestamp) VALUES(:id, :auteur, :cible, :timestamp)');
        $stmt->execute(['id' => Uuid::v7(), 'auteur' => $idAuteur, 'cible' => $idCible, 'timestamp' => $finaller]);
        // Supprimer ceux vieux de plus de 12 heures.
        $stmt = $pdo->prepare("DELETE FROM logatt WHERE timestamp < CURRENT_TIMESTAMP - INTERVAL '12 hours'");
        $stmt->execute();

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
        $stmt = $pdo->prepare('SELECT bouche, smack, baiser, pelle, tech1, tech2, langue, score FROM membres WHERE id = :id');
        $stmt->execute(['id' => $idAuteur]);
        $donnees_info3 = $stmt->fetch();
        $AttSmack = $donnees_info3['smack'];
        $AttBaiser = $donnees_info3['baiser'];
        $AttPelle = $donnees_info3['pelle'];
        $AttApnee = $donnees_info3['tech1'];
        $AttFlirt = $donnees_info3['tech2'];
        $AttBouche = $donnees_info3['bouche'];
        $AttLangue = $donnees_info3['langue'];
        $AttScore = $donnees_info3['score'];

        $stmt = $pdo->prepare('SELECT coeur, timestamp, bouche, amour, smack, baiser, pelle, tech3, dent, langue, bloque, score FROM membres WHERE id = :id');
        $stmt->execute(['id' => $idCible]);
        $donnees_info4 = $stmt->fetch();
        $DefSmack = $donnees_info4['smack'];
        $DefBaiser = $donnees_info4['baiser'];
        $DefPelle = $donnees_info4['pelle'];
        $DefCrachat = $donnees_info4['tech3'];
        $DefBouche = $donnees_info4['bouche'];
        $DefLangue = $donnees_info4['langue'];
        $DefDent = $donnees_info4['dent'];
        $DefBloque = $donnees_info4['bloque'];
        $DefScore = $donnees_info4['score'];

        // Gestion de l'attaque (coeff * bisous):
        $AttForce = (1 + (0.1 * $AttBouche) + (0.5 * $AttFlirt)) * ($AttSmack + (2.1 * $AttBaiser) + ((3.5 + 0.2 * $AttLangue) * $AttPelle));

        $DefForce = (1 + (0.1 * $DefBouche) + (0.7 * $DefDent)) * ($DefSmack + (2.1 * $DefBaiser) + ((3.5 + 0.2 * $DefLangue) * $DefPelle));
        // Si on est déjà en attaque, on diminue considérablement la force de défense.
        if (1 == $DefBloque) {
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
            if (0 == $DefBloque) {
                $DefSmack = floor($DefSmack * (1 - $coeffBilan / random_int(2, 10)));
                $DefBaiser = floor($DefBaiser * (1 - $coeffBilan / random_int(2, 10)));
                $DefPelle = floor($DefPelle * (1 - $coeffBilan / random_int(2, 10)));
            }

            // Attaque terminée, plus rien à voir.
            $stmt = $pdo->prepare('DELETE FROM attaque WHERE auteur = :auteur');
            $stmt->execute(['auteur' => $idAuteur]);
            // Envoyer un MP pour signifier les résultats.
            // On supprime les unités.
            $stmt = $pdo->prepare('UPDATE membres SET smack = :smack, baiser = :baiser, pelle = :pelle, bloque = FALSE WHERE id = :id');
            $stmt->execute(['smack' => $AttSmack, 'baiser' => $AttBaiser, 'pelle' => $AttPelle, 'id' => $idAuteur]);
            $stmt = $pdo->prepare('UPDATE membres SET smack = :smack, baiser = :baiser, pelle = :pelle WHERE id = :id');
            $stmt->execute(['smack' => $DefSmack, 'baiser' => $DefBaiser, 'pelle' => $DefPelle, 'id' => $idCible]);

            AdminMP($idAuteur, 'Quel rateau !!', "Bouuhh, t'as perdu tout tes bisous !!
			Tu n'as pas réussi à embrasser ton adversaire !!
			Il te reste :
			- 0 smacks
			- 0 baisers
			- 0 baisers langoureux
			");
            AdminMP($idCible, 'Bien esquivé !', "Bravo, tu ne t'es pas laissé faire !
			Il te reste :
			- ".$DefSmack.' smacks
			- '.$DefBaiser.' baisers
			- '.$DefPelle.' baisers langoureux
			');

            // Bien se défendre fait gagner des points.
            $addScore = 5000 * ($AttScore / $DefScore);
            AjouterScore($idCible, $addScore);
        } elseif (0 == $bilan) {
            $AttSmack = floor($AttSmack * (1 - 1 / random_int(2, 10)));
            $AttBaiser = floor($AttBaiser * (1 - 1 / random_int(2, 10)));
            // Gestion des dents, ca fait plutot mal...
            $dentsCoeff = $DefDent - $AttLangue;
            if ($dentsCoeff < 0) {
                $dentsCoeff = 0;
            }

            $AttPelle = floor($AttPelle * ((1 - 1 / random_int(2, 10)) * (1 - 0.1 * $dentsCoeff)));

            // Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
            if (0 == $DefBloque) {
                $DefSmack = floor($DefSmack * (1 - 1 / random_int(2, 10)));
                $DefBaiser = floor($DefBaiser * (1 - 1 / random_int(2, 10)));
                $DefPelle = floor($DefPelle * (1 - 1 / random_int(2, 10)));
            }

            // Ca retourne, pas de blocage
            $stmt = $pdo->prepare('UPDATE membres SET smack = :smack, baiser = :baiser, pelle = :pelle WHERE id = :id');
            $stmt->execute(['smack' => $AttSmack, 'baiser' => $AttBaiser, 'pelle' => $AttPelle, 'id' => $idAuteur]);
            $stmt = $pdo->prepare('UPDATE membres SET smack = :smack, baiser = :baiser, pelle = :pelle WHERE id = :id');
            $stmt->execute(['smack' => $DefSmack, 'baiser' => $DefBaiser, 'pelle' => $DefPelle, 'id' => $idCible]);

            AdminMP($idAuteur, 'Ex Aequo', "Egalité parfaite lors de ta dernière tentative.
			Tu ne ramène pas de points d'amour !!
			Il te reste :
			- ".$AttSmack.' smacks
			- '.$AttBaiser.' baisers
			- '.$AttPelle.' baisers langoureux
			');
            AdminMP($idCible, 'Ex Aequo', "Egalité parfaite contre le joueur qui voulait t'embrasser.
			Il te reste :
			- ".$DefSmack.' smacks
			- '.$DefBaiser.' baisers
			- '.$DefPelle.' baisers langoureux
			');
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
            if (0 == $DefBloque) {
                $DefSmack = floor($DefSmack * ($coeffBilan / 2));
                $DefBaiser = floor($DefBaiser * ($coeffBilan / 2));
                $DefPelle = floor($DefPelle * ($coeffBilan / 2));
            }

            // Faire retourner, Avec butin.

            // Gestion du butin
            if ($idCible == $id && true === $_SESSION['logged']) {
                $DefAmour = $amour;
            } else {
                $DefTimestamp = $castToUnixTimestamp->fromPgTimestamptz($donnees_info4['timestamp']);
                $DefCoeur = $donnees_info4['coeur'];
                $DefAmour = $donnees_info4['amour'];
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

            if ($idCible == $id && true === $_SESSION['logged']) {
                $amour = $DefAmour;
            }

            // Ca retourne, pas de blocage
            $stmt = $pdo->prepare('UPDATE membres SET smack = :smack, baiser = :baiser, pelle = :pelle WHERE id = :id');
            $stmt->execute(['smack' => $AttSmack, 'baiser' => $AttBaiser, 'pelle' => $AttPelle, 'id' => $idAuteur]);
            $stmt = $pdo->prepare('UPDATE membres SET amour = :amour, smack = :smack, baiser = :baiser, pelle = :pelle WHERE id = :id');
            $stmt->execute(['amour' => (int) $DefAmour, 'smack' => $DefSmack, 'baiser' => $DefBaiser, 'pelle' => $DefPelle, 'id' => $idCible]);

            $stmt = $pdo->prepare('UPDATE attaque SET butin = :butin WHERE auteur = :auteur');
            $stmt->execute(['butin' => $butin, 'auteur' => $idAuteur]);

            AdminMP($idAuteur, "Tu l'as embrassé !!", 'Bravo, tu as réussi à embrasser ton adversaire.
			Tes bisous seront bientôt revenus près de toi.
			Tu as réussi à prendre '.$butin." Points d'Amour !!
			Il te reste :
			- ".$AttSmack.' smacks
			- '.$AttBaiser." baisers
			- {$AttPelle} baisers langoureux
			");
            AdminMP($idCible, "Tu t'es fait embrasser", "Tu n'as pas su résister à ses Bisous !!
			Tu t'es fait prendre ".$butin." Points d'Amour !!
			Il te reste :
			- ".$DefSmack.' smacks
			- '.$DefBaiser.' baisers
			- '.$DefPelle.' baisers langoureux
			');

            // Bien attaquer fait gagner des points.
            $addScore = 10000 * ($DefScore / $AttScore) + ($butin / 10);
            AjouterScore($idAuteur, $addScore);
        }
    }

    // Phase retour
    $sql_info = $pdo->query("SELECT auteur, butin FROM attaque WHERE finretour <= CURRENT_TIMESTAMP AND state IN ('ComingBack', 'CalledOff')");
    while ($donnees_info = $sql_info->fetch()) {
        $idAuteur = $donnees_info['auteur'];
        $butinAuteur = $donnees_info['butin'];
        $stmt = $pdo->prepare('DELETE FROM attaque WHERE auteur = :auteur');
        $stmt->execute(['auteur' => $idAuteur]);

        if ($idAuteur == $id && true === $_SESSION['logged']) {
            $AttAmour = $amour;
        } else {
            $stmt = $pdo->prepare('SELECT amour FROM membres WHERE id = :id');
            $stmt->execute(['id' => $idAuteur]);
            $donnees_info3 = $stmt->fetch();
            $AttAmour = $donnees_info3['amour'];
        }

        // On fais pas de mise à jour du nb de points d'amour, pas besoin.
        // Récupération des points d'amour.
        $AttAmour += $butinAuteur;

        if ($idAuteur == $id && true === $_SESSION['logged']) {
            $amour = $AttAmour;
            $joueurBloque = 0;
        }

        // Libérer l'auteur et ajouter butin
        $stmt = $pdo->prepare('UPDATE membres SET bloque = FALSE, amour = :amour WHERE id = :id');
        $stmt->execute(['amour' => (int) $AttAmour, 'id' => $idAuteur]);
    }
}
