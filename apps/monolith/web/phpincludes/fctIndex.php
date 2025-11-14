<?php

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

function AdminMP($cible, $objet, $message, $lu = 0): void
{
    $pdo = bd_connect();
    $message = nl2br((string) $message);

    $stmt = $pdo->prepare('SELECT COUNT(*) AS nbmsg FROM messages WHERE destin = :destin');
    $stmt->execute(['destin' => $cible]);

    $nbmsg = $stmt->fetchColumn();
    if ($nbmsg >= 20) {
        $Asuppr = $nbmsg - 19;
        $date48 = time() - 172800;
        $stmt = $pdo->prepare('DELETE FROM messages WHERE destin = :destin AND timestamp <= :timestamp ORDER BY id LIMIT :limit');
        $stmt->execute(['destin' => $cible, 'timestamp' => $date48, 'limit' => $Asuppr]);
    }

    $timestamp = time();
    $stmt = $pdo->prepare(
        'INSERT INTO messages'
        .' (posteur, destin, message, timestamp, statut, titre)'
        .' VALUES(1, :destin, :message, :timestamp, :statut, :titre)',
    );
    $stmt->execute(['destin' => $cible, 'message' => $message, 'timestamp' => $timestamp, 'statut' => $lu, 'titre' => $objet]);
}

function SupprimerCompte($idCompteSuppr): void
{
    $pdo = bd_connect();
    $stmt = $pdo->prepare('DELETE FROM membres WHERE id = :id');
    $stmt->execute(['id' => $idCompteSuppr]);
    $stmt = $pdo->prepare('DELETE FROM messages WHERE destin = :destin');
    $stmt->execute(['destin' => $idCompteSuppr]);
    $stmt = $pdo->prepare('DELETE FROM messages WHERE auteur = :auteur');
    $stmt->execute(['auteur' => $idCompteSuppr]);
    $stmt = $pdo->prepare('DELETE FROM evolution WHERE auteur = :auteur');
    $stmt->execute(['auteur' => $idCompteSuppr]);
    $stmt = $pdo->prepare('DELETE FROM liste WHERE auteur = :auteur');
    $stmt->execute(['auteur' => $idCompteSuppr]);
    $stmt = $pdo->prepare('DELETE FROM logatt WHERE auteur = :auteur');
    $stmt->execute(['auteur' => $idCompteSuppr]);
    // Attaques a gerer.
    $stmt = $pdo->prepare('SELECT auteur FROM attaque WHERE cible = :cible');
    $stmt->execute(['cible' => $idCompteSuppr]);
    while ($donnees_info = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('UPDATE membres SET bloque = 0 WHERE id = :id');
        $stmt2->execute(['id' => $donnees_info['auteur']]);
        $stmt2 = $pdo->prepare('DELETE FROM attaque WHERE auteur = :auteur');
        $stmt2->execute(['auteur' => $donnees_info['auteur']]);
        AdminMP($donnees_info['auteur'], 'Pas de chance', 'Ta cible vient de supprimer son compte.
			Une prochaine fois, peut-etre...');
    }

    $stmt = $pdo->prepare('SELECT cible FROM attaque WHERE auteur = :auteur');
    $stmt->execute(['auteur' => $idCompteSuppr]);
    if ($donnees_info = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('DELETE FROM attaque WHERE auteur = :auteur');
        $stmt2->execute(['auteur' => $idCompteSuppr]);
        AdminMP($donnees_info['cible'], 'Veinard !!', 'Tu as vraiment de la chance !!
			Ton agresseur vient de supprimer son compte, tu peux donc dormir tranquille.');
    }
}

// Presuppose que toutes les verifications ont ete faites.
function ChangerMotPasse($idChange, $newMdp): void
{
    $pdo = bd_connect();
    $newMdp = md5((string) $newMdp);
    $stmt = $pdo->prepare('UPDATE membres SET mdp = :mdp WHERE id = :id');
    $stmt->execute(['mdp' => $newMdp, 'id' => $idChange]);
}

// Presuppose que toutes les verifications ont ete faites.
function AjouterScore($idScore, $valeur): void
{
    $pdo = bd_connect();
    $stmt = $pdo->prepare('SELECT score FROM membres WHERE id = :id');
    $stmt->execute(['id' => $idScore]);

    $donnees_info = $stmt->fetch();
    $stmt = $pdo->prepare('UPDATE membres SET score = :score WHERE id = :id');
    $stmt->execute(['score' => $donnees_info['score'] + $valeur, 'id' => $idScore]);
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
    $sql_info = $pdo->query('SELECT nombre FROM nuage WHERE id=1');
    $donnees_info = $sql_info->fetch();
    $NbNuages = $donnees_info['nombre'];

    $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_pos FROM membres WHERE nuage = :nuage');
    $stmt->execute(['nuage' => $NbNuages]);

    $nbPos = $stmt->fetchColumn();

    // Neuf personnes par nuage max, lors de l'attribution.
    if ($nbPos > 8) {
        ++$NbNuages;
        $stmt = $pdo->prepare('UPDATE nuage SET nombre = :nombre WHERE id = 1');
        $stmt->execute(['nombre' => $NbNuages]);
        $nbPos = 0;
    }

    if ($nbPos > 0) {
        $OccPos = [];

        $stmt = $pdo->prepare('SELECT position FROM membres WHERE nuage = :nuage');
        $stmt->execute(['nuage' => $NbNuages]);
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
    $stmt = $pdo->prepare('UPDATE membres SET nuage = :nuage, position = :position WHERE id = :id');
    $stmt->execute(['nuage' => $NbNuages, 'position' => $FinalPos, 'id' => $idJoueur]);
}
