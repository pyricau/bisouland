<?php

use Symfony\Component\Uid\Uuid;

// Fonction pour calculer un temps en millisecondes.
function microtime_float(): int|float
{
    return array_sum(explode(' ', microtime()));
}

function calculterAmour($CalAmour, $timeDiff, $LvlCoeur, $nb1, $nb2, $nb3)
{
    $CalAmour = calculerGenAmour($CalAmour, $timeDiff, $LvlCoeur, $nb1, $nb2, $nb3);
    // Cette fonction ajoute un frein sur le minima.
    if ($CalAmour < 0) {
        return 0;
    }

    return $CalAmour;
}

function calculerGenAmour($CalAmour, $timeDiff, $LvlCoeur, $nb1, $nb2, $nb3)
{
    $diff = $LvlCoeur - (0.3 * $nb1 + 0.7 * $nb2 + $nb3);
    if ($diff > 0) {
        // 2 equations :  lvl 50 : 100 000 par heure et lvl 20  : 20000 par heure.
        $CalAmour += (ExpoSeuil(5500, 6, $diff) * $timeDiff) / 3600;
    } elseif ($diff < 0) {
        $CalAmour -= (ExpoSeuil(5500, 6, -1 * $diff) * $timeDiff) / 3600;
    }

    return $CalAmour;
}

// Permet de convertir un timestamp en chaine sous la forme heure:minutes:secondes.
function strTemps($s): string
{
    $m = 0;
    $h = 0;
    if ($s < 0) {
        return '0:00:00';
    }

    if ($s > 59) {
        $m = floor($s / 60);
        $s = $s - $m * 60;
    }

    if ($m > 59) {
        $h = floor($m / 60);
        $m = $m - $h * 60;
    }

    $ts = $s;
    $tm = $m;
    if ($s < 10) {
        $ts = '0'.$s;
    }

    if ($m < 10) {
        $tm = '0'.$m;
    }

    if ($h > 24) {
        $d = floor($h / 24);
        $h = $h - $d * 24;
        $h = $d.' jours '.$h;
    }

    return $h.' h '.$tm.' min '.$ts.' sec';
}

// Renvoi un s (ou^$lettre) si le nombre est plus grand que 1, renvoi '' (ou $alt) sinon.
function pluriel($nombre, $lettre = 's', $alt = '')
{
    return ($nombre > 1) ? $lettre : $alt;
}

function expo($a, $b, $val, $int = 0): float
{
    $ret = $a * exp($b * $val);

    if (1 == $int) {
        return ceil($ret);
    }

    return $ret;
}

// Val doit etre different de 0.
function InvExpo($a, $b, $val, $int = 0): float
{
    // Patch to avoid division by 0...
    if (0 == $val) {
        $val = 1;
    }

    $ret = $a * exp($b / $val);

    if (1 == $int) {
        return ceil($ret);
    }

    return $ret;
}

// Plus a augmente, plus on augmente la valeur de seuil
// Plus b augmente, plus on eloigne le moment ou on atteint le seuil .
function ExpoSeuil($a, $b, $val, $int = 0): float
{
    if ($val <= 0) {
        $val = 1;
    }

    $ret = $a * exp((-1 * $b) / $val);

    if (1 == $int) {
        return ceil($ret);
    }

    return $ret;
}

function AdminMP($cible, $objet, $message, bool $lu = false): void
{
    $pdo = bd_connect();
    $castToPgBoolean = cast_to_pg_boolean();
    $message = nl2br((string) $message);

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT COUNT(*) AS nbmsg
        FROM messages
        WHERE destin = :destin
    SQL);
    $stmt->execute([
        'destin' => $cible,
    ]);

    $nbmsg = $stmt->fetchColumn();
    if ($nbmsg >= 20) {
        $Asuppr = $nbmsg - 19;
        $stmt = $pdo->prepare(<<<'SQL'
            DELETE FROM messages
            WHERE id IN (
                SELECT id
                FROM messages
                WHERE destin = :destin
                AND timestamp <= CURRENT_TIMESTAMP - INTERVAL '48 hours'
                ORDER BY id
                LIMIT :limit
            )
        SQL);
        $stmt->execute([
            'destin' => $cible,
            'limit' => $Asuppr,
        ]);
    }

    $stmt = $pdo->prepare(<<<'SQL'
        INSERT INTO messages
        (id, destin, message, timestamp, statut, titre)
        VALUES (:id, :destin, :message, CURRENT_TIMESTAMP, :statut, :titre)
    SQL);
    $stmt->execute([
        'id' => Uuid::v7(),
        'destin' => $cible,
        'message' => $message,
        'statut' => $castToPgBoolean->from($lu),
        'titre' => $objet,
    ]);
}

/**
 * @param string $accountId an AccountId (UUID)
 */
function SupprimerCompte(string $accountId): void
{
    $pdo = bd_connect();

    $pdo->beginTransaction();

    // First unblock and notify Players who sent BlownKisses to this AccountId
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT auteur AS sender_id
        FROM attaque
        WHERE cible = :account_id
    SQL);
    $stmt->execute([
        'account_id' => $accountId,
    ]);
    /**
     * @var array<int, array{
     *      sender_id: string, // UUID
     * }> $incomingBlownKisses
     */
    $incomingBlownKisses = $stmt->fetchAll();
    if ([] !== $incomingBlownKisses) {
        // Unblock Players who sent BlownKisses to this AccountId
        $senderIds = array_column($incomingBlownKisses, 'sender_id');

        $inSize = count($incomingBlownKisses);
        $inValues = implode(', ', array_fill(0, $inSize, '?'));

        $accountIdIn = "id IN ({$inValues})";
        $stmt = $pdo->prepare(<<<SQL
            UPDATE membres
            SET bloque = FALSE
            WHERE {$accountIdIn}
        SQL);
        $stmt->execute($senderIds);

        // Notify Players that their target deleted their account
        foreach ($senderIds as $senderId) {
            AdminMP(
                $senderId,
                'Pas de chance',
                "Ta cible vient de supprimer son compte.\n"
               .'Une prochaine fois, peut-etre...',
            );
        }
    }

    // Next notify the Player who was going to receive BlownKiss from this AccountId
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT cible AS receiver_id
        FROM attaque
        WHERE auteur = :account_id
    SQL);
    $stmt->execute([
        'account_id' => $accountId,
    ]);
    /**
     * @var array{
     *      receiver_id: string, // UUID
     * }|false $sentBlownKiss
     */
    $sentBlownKiss = $stmt->fetch();
    if (false !== $sentBlownKiss) {
        AdminMP(
            $sentBlownKiss['receiver_id'],
            'Veinard !!',
            "Tu as vraiment de la chance !!\n"
            .'Ton agresseur vient de supprimer son compte, tu peux donc dormir tranquille.',
        );
    }

    // Finally delete the Account
    $stmt = $pdo->prepare(<<<'SQL'
        DELETE FROM membres
        WHERE id = :account_id
    SQL);
    $stmt->execute([
        'account_id' => $accountId,
    ]);

    try {
        $pdo->commit();
    } catch (PDOException $pdoException) {
        $pdo->rollBack();

        throw $pdoException;
    }
}

// Presuppose que toutes les verifications ont ete faites.
function ChangerMotPasse($idChange, $newMdp): void
{
    $pdo = bd_connect();
    $newMdp = password_hash($newMdp, \PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(<<<'SQL'
        UPDATE membres
        SET mdp = :password_hash
        WHERE id = :account_id
    SQL);
    $stmt->execute([
        'password_hash' => $newMdp,
        'account_id' => $idChange,
    ]);
}

// Presuppose que toutes les verifications ont ete faites.
function AjouterScore($idScore, $valeur): void
{
    $pdo = bd_connect();
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT score
        FROM membres
        WHERE id = :account_id
    SQL);
    $stmt->execute([
        'account_id' => $idScore,
    ]);
    /**
     * @var array{
     *      score: int,
     * }|false $account
     */
    $account = $stmt->fetch();
    if (false !== $account) {
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE membres
            SET score = :score
            WHERE id = :account_id
        SQL);
        $stmt->execute([
            'score' => (int) ($account['score'] + $valeur),
            'account_id' => $idScore,
        ]);
    }
}

function formaterNombre($nombre): string
{
    return number_format($nombre, 0, ',', ' ');
}

function distanceMax($coeur, $jambes): int|float
{
    return $coeur + 8 * $jambes;
}

// Fonction qui retourne 0 si joueurAutre est meme niveau, 1 s'il est intouchable parce que trop faible, 2 s'il est intouchable parce que trop fort.
function voirNiveau($scoreJoueur, $scoreAutre): int
{
    if ($scoreJoueur < 50) {
        return 2;
    }

    if ($scoreAutre < 50) {
        return 1;
    }

    if ($scoreJoueur > 2000 && $scoreAutre > 2000) {
        return 0;
    }

    if (abs($scoreAutre - $scoreJoueur) <= 200) {
        return 0;
    }

    if ($scoreJoueur - $scoreAutre > 200) {
        return 1;
    }

    return 2;
}

// transformation de bbcode smiley en images.
function smileys($texte): string|array
{
    $in = [
        'o_O',
        ';)',
        ':D',
        '^^',
        ':o',
        ':p',
        ':colere:',
        ':noel:',
        ':)',
        ':lol:',
        ':-&deg;',
        ':(',
        ':euh:',
        ':coeur:',
    ];

    $out = [
        '<img src="smileys/blink.gif" alt="un smiley" title="o_O"/>',
        '<img src="smileys/clin.png" alt="un smiley" title=";)"/>',
        '<img src="smileys/heureux.png" alt="un smiley" title=":D"/>',
        '<img src="smileys/hihi.png" alt="un smiley" title="^^"/>',
        '<img src="smileys/huh.png" alt="un smiley" title=":o"/>',
        '<img src="smileys/langue.png" alt="un smiley" title=":p"/>',
        '<img src="smileys/mechant.png" alt="un smiley" title=":colere:"/>',
        '<img src="smileys/noel.png" alt="un smiley" title=":noel:"/>',
        '<img src="smileys/smile.png" alt="un smiley" title=":)"/>',
        '<img src="smileys/rire.gif" alt="un smiley" title=":lol:"/>',
        '<img src="smileys/siffle.png" alt="un smiley" title=":-&deg;"/>',
        '<img src="smileys/triste.png" alt="un smiley" title=":("/>',
        '<img src="smileys/unsure.gif" alt="un smiley" title=":euh:"/>',
        '<img src="images/puce.png" alt="un smiley" title=":coeur:"/>',
    ];

    return str_replace($in, $out, $texte);
}

function bbLow($text): string|array
{
    $bbcode = [
        '[b]', '[/b]',
        '[u]', '[/u]',
        '[i]', '[/i]',
    ];
    $htmlcode = [
        '<strong>', '</strong>',
        '<u>', '</u>',
        '<em>', '</em>',
    ];

    $text = stripslashes((string) $text);

    $text = str_replace($bbcode, $htmlcode, $text);

    $text = preg_replace('!\[color=(red|green|blue|yellow|purple|olive|white|black)\](.+)\[/color\]!isU', '<span style="color:$1">$2</span>', $text);
    $text = preg_replace('!\[size=(xx-small|x-small|small|medium|large|x-large|xx-large)\](.+)\[/size\]!isU', '<span style="font-size:$1">$2</span>', (string) $text);

    return smileys($text);
}

function tempsAttaque($distance, $jambes): float
{
    return floor(($distance * 1000) / (1 + 0.3 * $jambes));
}

function coutAttaque($distance, $jambes): float
{
    $exp = $distance - $jambes;
    if ($exp < 0) {
        $exp = 0;
    }

    return expo(100, 0.4, $exp, 1);
}

function GiveNewPosition($idJoueur): void
{
    $pdo = bd_connect();
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT nombre
        FROM nuage
        WHERE id = :nuage_config_id
    SQL);
    $stmt->execute([
        'nuage_config_id' => '00000000-0000-0000-0000-000000000002',
    ]);
    /**
     * @var array{
     *      nombre: int,
     * }|false $nuageConfig
     */
    $nuageConfig = $stmt->fetch();
    $NbNuages = $nuageConfig['nombre'];

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT COUNT(*) AS total_accounts_in_nuage
        FROM membres
        WHERE nuage = :nuage
    SQL);
    $stmt->execute([
        'nuage' => $NbNuages,
    ]);
    /**
     * @var array{
     *      total_accounts_in_nuage: int,
     * }|false $result
     */
    $result = $stmt->fetch();
    $nbPos = (false !== $result) ? $result['total_accounts_in_nuage'] : 0;

    // Neuf personnes par nuage max, lors de l'attribution.
    if ($nbPos > 8) {
        ++$NbNuages;
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE nuage
            SET nombre = :nombre
            WHERE id = :nuage_config_id
        SQL);
        $stmt->execute([
            'nombre' => $NbNuages,
            'nuage_config_id' => '00000000-0000-0000-0000-000000000002',
        ]);
        $nbPos = 0;
    }

    if ($nbPos > 0) {
        $OccPos = [];

        $stmt = $pdo->prepare(<<<'SQL'
            SELECT position
            FROM membres
            WHERE nuage = :nuage
        SQL);
        $stmt->execute([
            'nuage' => $NbNuages,
        ]);
        $i = 0;
        // On récupère les positions occupées.
        while ($donnees_info = $stmt->fetch()) {
            $OccPos[$i] = $donnees_info['position'];
            ++$i;
        }

        $FreePos = [];

        $nbLibre = 16 - $nbPos;

        $j = 0;

        // Rempli FreePos avec les positions libres
        for ($i = 1; $i <= 16; ++$i) {
            if (!in_array($i, $OccPos)) {
                $FreePos[$j] = $i;
                ++$j;
            }
        }

        // On choisi une valeur au hasard.

        $FinalPos = $FreePos[random_int(0, $nbLibre - 1)];
    } else {
        $FinalPos = random_int(1, 16);
    }

    // On enregistre.
    $stmt = $pdo->prepare(<<<'SQL'
        UPDATE membres
        SET nuage = :nuage, position = :position
        WHERE id = :account_id
    SQL);
    $stmt->execute([
        'nuage' => $NbNuages,
        'position' => $FinalPos,
        'account_id' => $idJoueur,
    ]);
}
