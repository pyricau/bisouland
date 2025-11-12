<?php

function arbre($classe, $type, $nbE)
{
    if (0 == $classe) {
        if (0 == $type) {
            // coeur
            return true;
        }

        if (1 == $type) {
            // bouche
            if ($nbE[0][0] >= 2) {
                return true;
            }
        } elseif (2 == $type) {
            // langue
            if ($nbE[0][1] >= 2 && $nbE[0][0] >= 5) {
                return true;
            }
        } elseif (3 == $type) {
            // dent
            if ($nbE[0][1] >= 2) {
                return true;
            }
        } elseif (4 == $type) {
            // jambes
            if ($nbE[0][0] >= 15) {
                return true;
            }
        } elseif (5 == $type) {
            // oeil
            if ($nbE[0][0] >= 10) {
                return true;
            }
        }
    } elseif (1 == $classe) {
        if (0 == $type) {
            // smack
            if ($nbE[0][1] >= 2) {
                return true;
            }
        } elseif (1 == $type) {
            // baiser
            if ($nbE[0][1] >= 6) {
                return true;
            }
        } elseif (2 == $type) {
            // baiser langoureux
            if ($nbE[0][2] >= 5 && $nbE[0][1] >= 10) {
                return true;
            }
        }
    } elseif (2 == $classe) {
        if (0 == $type) {
            // Apnée
            if ($nbE[0][0] >= 3 && $nbE[0][1] >= 2) {
                return true;
            }
        } elseif (1 == $type) {
            // Surprise
            if ($nbE[0][0] >= 5 && $nbE[0][1] >= 4) {
                return true;
            }
        } elseif (2 == $type) {
            // Crachat
            if ($nbE[2][0] >= 1 && $nbE[2][1] >= 3 && $nbE[0][2] >= 3) {
                return true;
            }
        } elseif (3 == $type) {
            // Saut
            if ($nbE[0][4] >= 2) {
                return true;
            }
        } elseif (4 == $type) {
            // Soupe
            if ($nbE[0][0] >= 15 && $nbE[0][1] >= 8 && $nbE[0][2] >= 4) {
                return true;
            }
        }
    }

    return false;
}

if (isset($inMainPage) && true == $inMainPage) {
    $pdo = bd_connect();

    // Nombre de type différents pour la classe concernée.
    $nbEvol = $nbType[$evolPage];
    $evolution = -1; // Valeur par défaut ( = aucune construction en cours).

    // Annuler une construction ne permet pas de récupérer les points.
    if (isset($_POST['cancel']) || isset($_GET['cancel'])) {
        $classeCancel = $evolPage;
        $stmt = $pdo->prepare('SELECT cout FROM evolution WHERE auteur = :auteur AND classe = :classe');
        $stmt->execute(['auteur' => $id, 'classe' => $classeCancel]);
        $donnees_info = $stmt->fetch();
        $amour += ($donnees_info['cout'] / 2);
        $stmt = $pdo->prepare('DELETE FROM evolution WHERE auteur = :auteur AND classe = :classe');
        $stmt->execute(['auteur' => $id, 'classe' => $classeCancel]);

        // On passe à une nouvelle construction si disponible.
        $stmt = $pdo->prepare('SELECT id, duree, type, cout FROM liste WHERE auteur = :auteur AND classe = :classe ORDER BY id LIMIT 0,1');
        $stmt->execute(['auteur' => $id, 'classe' => $classeCancel]);
        if ($donnees_info = $stmt->fetch()) {
            $timeFin2 = time() + $donnees_info['duree'];
            $stmt2 = $pdo->prepare('INSERT INTO evolution (timestamp, classe, type, auteur, cout) VALUES (:timestamp, :classe, :type, :auteur, :cout)');
            $stmt2->execute(['timestamp' => $timeFin2, 'classe' => $classeCancel, 'type' => $donnees_info['type'], 'auteur' => $id, 'cout' => $donnees_info['cout']]);
            $stmt2 = $pdo->prepare('DELETE FROM liste WHERE id = :id');
            $stmt2->execute(['id' => $donnees_info['id']]);

            if (1 == $classeCancel) {
                // $amour -= $donnees_info['cout'];
            }
        }
    }

    // On détermine s'il y a une construction en cours.
    $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_id FROM evolution WHERE auteur = :auteur AND classe = :classe');
    $stmt->execute(['auteur' => $id, 'classe' => $evolPage]);
    if (0 != $stmt->fetchColumn()) {
        // Si oui, on récupère les infos sur la construction.
        $stmt = $pdo->prepare('SELECT timestamp, type FROM evolution WHERE auteur = :auteur AND classe = :classe');
        $stmt->execute(['auteur' => $id, 'classe' => $evolPage]);
        $donnees_info = $stmt->fetch();
        // Date a laquelle la construction sera terminée.
        $timeFin = $donnees_info['timestamp'];
        // Type de la construction.
        $evolution = $donnees_info['type'];

        // partie qui permet d'ajouter des constructions si il ya déjà des constructions en cours.
        $i = 0;
        $stop = 0;
        if (1 == $joueurBloque && 1 == $evolPage) {
            $stop = 1;
        }

        while ($i != $nbEvol && 0 === $stop) {
            // Pour l'instant, on gère ca que pour les bisous.
            if (isset($_POST[$Obj[$evolPage][$i]]) && 1 == $evolPage && ($amour >= $amourE[$evolPage][$i] && arbre($evolPage, $i, $nbE))) {
                $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_id FROM liste WHERE auteur = :auteur AND classe = 1');
                $stmt->execute(['auteur' => $id]);
                if ($stmt->fetchColumn() < 9) {
                    // Construction demandée, donc on arrete la boucle.
                    $stop = 1;
                    $dureeConst = $tempsE[$evolPage][$i];
                    $stmt2 = $pdo->prepare('INSERT INTO liste (duree, classe, type, auteur, cout) VALUES (:duree, :classe, :type, :auteur, :cout)');
                    $stmt2->execute(['duree' => $dureeConst, 'classe' => $evolPage, 'type' => $i, 'auteur' => $id, 'cout' => $amourE[$evolPage][$i]]);
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
        if (1 == $joueurBloque && 1 == $evolPage) {
            $stop = 1;
        }

        while ($i != $nbEvol && 0 === $stop) {
            // On regarde si on a demandé la construction, et si on a le nombre de points d'amour nécessaire.
            // (La vérification du nombre de points d'amour permet d'éviter les tricheurs --> sécurité)
            if (isset($_POST[$Obj[$evolPage][$i]]) && $amour >= $amourE[$evolPage][$i] && arbre($evolPage, $i, $nbE)) {
                // Construction demandée, donc on arrete la boucle.
                $stop = 1;
                // On calcule la date de fin du calcul (servira aussi pour l'affichage sur la page).
                $timeFin = time() + $tempsE[$evolPage][$i];
                // On met l'objet en construction. id non définie car auto incrémentée.
                // Le champ id est peut etre a supprimer.
                $stmt = $pdo->prepare('INSERT INTO evolution (timestamp, classe, type, auteur, cout) VALUES (:timestamp, :classe, :type, :auteur, :cout)');
                $stmt->execute(['timestamp' => $timeFin, 'classe' => $evolPage, 'type' => $i, 'auteur' => $id, 'cout' => $amourE[$evolPage][$i]]);
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
