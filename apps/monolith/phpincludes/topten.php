<?php

use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;

$nbTop = 20; ?>
<h1>Top <?php echo $nbTop; ?></h1>
<h2>Liste des <?php echo $nbTop; ?> meilleurs joueurs de BisouLand</h2>
<center><table width="80%">
   <tr>
		<th width="10%">Position</th>
<?php
if (true === $blContext['is_signed_in']) {
    echo '
		<th width="5%"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/onoff.png" alt="Statut" title="" /><span>Statut de connexion du joueur</span></a></th>
		<th width="45%">Nom</th>
		<th width="20%">Points</th>
		<th width="20%">Actions</th>
		';
} else {
    echo '
		<th width="5%"><a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/onoff.png" alt="Statut" title="" /><span>Statut de connexion du joueur</span></a></th>
		<th width="65%">Nom</th>
		<th width="20%">Points</th>
		';
}
?>
   </tr>
<?php
$pdo = bd_connect();

// Si on est loggué (et qu'on peut attaquer, on calcule notre position
if (
    true === $blContext['is_signed_in']
    && (
        $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]
    ) > 0
) {
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT position
        FROM membres
        WHERE id = :current_account_id
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /** @var array{
     *      position: int,
     *  }|false $results
     */
    $results = $stmt->fetch();
    $positionSource = $results['position'];
}

$stmt = $pdo->prepare(<<<'SQL'
    SELECT id, pseudo, nuage, position, score, lastconnect
    FROM membres
    ORDER BY score DESC
    LIMIT :limit OFFSET 0
SQL);
$stmt->execute([
    'limit' => $nbTop,
]);
/**
 * @var array<int, array{
 *      id: string, // UUID
 *      pseudo: string,
 *      nuage: int,
 *      position: int,
 *      score: int,
 *      lastconnect: string, // ISO 8601 timestamp string
 * }> $players
 */
$players = $stmt->fetchAll();
foreach ($players as $i => $player) {
    $rank = $i + 1;
    echo '<tr>
				<td>'.$rank.'</td>
				<td>';

    $lastConnection = new DateTimeImmutable($player['lastconnect']);
    $fiveMinutesAgo = new DateTimeImmutable('-5 minutes');
    if ($lastConnection > $fiveMinutesAgo) {
        echo ' <a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$player['pseudo'],' est connect&eacute;</span></a>';
    } else {
        echo ' <a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$player['pseudo']," n'est pas connect&eacute;</span></a>";
    }

    echo '</td>
				<td>';

    echo $player['pseudo'].'</td>
				<td>'.formaterNombre(floor($player['score'] / 1000.)).'</td>
				';

    if (true === $blContext['is_signed_in']) {
        echo '<td>';
        echo '<a class="bulle" href="',$player['nuage'],'.nuage.html" >
			<img src="images/nuage.png" title="" alt="" /><span>Nuage : ',$player['nuage'],'</span></a></td>';
    }

    echo '</tr>';
}
?>

</table></center>
