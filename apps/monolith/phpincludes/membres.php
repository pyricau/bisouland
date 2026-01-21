<h1>Liste des joueurs</h1>
<?php
$pdo = bd_connect();
$stmt = $pdo->query(<<<'SQL'
    SELECT COUNT(*) AS total_confirmed_members
    FROM membres
    WHERE confirmation = TRUE
SQL);
/** @var array{total_confirmed_members: int}|false $result */
$result = $stmt->fetch();
$total = (false !== $result) ? $result['total_confirmed_members'] : 0;

echo 'Nombre de membres : '.$total.'<br /><br />';

$nombreParPage = 15;

// On calcule le nombre de pages à créer
$nombreDePages = ceil($total / $nombreParPage);

if (isset($_GET['num'])) {
    $num = (int) $_GET['num'];
    if ($num > $nombreDePages) {
        $num = $nombreDePages;
    } elseif ($num < 1) {
        $num = 1;
    }
} else { // La variable n'existe pas, c'est la première fois qu'on charge la page
    $num = 1; // On se met sur la page 1 (par défaut)
}

// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premier = ($num - 1) * $nombreParPage;

$stmt = $pdo->prepare(<<<'SQL'
    SELECT
        id,
        pseudo,
        nuage,
        lastconnect
    FROM membres
    WHERE confirmation = TRUE
    ORDER BY id DESC
    LIMIT :results_per_page
    OFFSET :results_offset
SQL);
$stmt->execute([
    'results_per_page' => $nombreParPage,
    'results_offset' => $premier,
]);

if ($nombreDePages > 1) {
    echo '<center>Page :';
    for ($i = 1; $i <= $nombreDePages; ++$i) {
        if ($i != $num) {
            echo '<a href="membres.'.$i.'.html">'.$i.'</a> ';
        } else {
            echo ' '.$i.' ';
        }
    }
    echo '</center><br />';
}

/**
 * @var array<array{
 *      id: string, // UUID
 *      pseudo: string,
 *      nuage: int,
 *      lastconnect: string, // ISO 8601 timestamp string
 * }> $members
 */
$members = $stmt->fetchAll();
if (true === $blContext['is_signed_in']) {
    foreach ($members as $member) {
        $member['pseudo'] = stripslashes((string) $member['pseudo']);
        if ($member['lastconnect'] > time() - 300) {
            echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$member['pseudo'],' est connect&eacute;</span></a> ';
        } else {
            echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$member['pseudo']," n'est pas connect&eacute;</span></a> ";
        }
        echo '<a class="bulle" href="',$member['nuage'],'.nuage.html" >
		<img src="images/nuage.png" title="" alt="" /><span>Nuage : ',$member['nuage'],'</span></a>
		<strong> ',$member['pseudo'],'</strong>
		<br />';
    }
} else {
    foreach ($members as $member) {
        $member['pseudo'] = stripslashes((string) $member['pseudo']);
        if ($member['lastconnect'] > time() - 300) {
            echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$member['pseudo'],' est connect&eacute;</span></a> ';
        } else {
            echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$member['pseudo']," n'est pas connect&eacute;</span></a> ";
        }
        echo '<strong>'.$member['pseudo'].'</strong><br />';
    }
}
?>
