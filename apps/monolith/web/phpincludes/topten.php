<?php $nbTop = 20; ?>
<h1>Top <?php echo $nbTop; ?></h1>
<h2>Liste des <?php echo $nbTop; ?> meilleurs joueurs de BisouLand</h2>
<center><table width="80%">
   <tr>
		<th width="10%">Position</th>
<?php
if (true == $_SESSION['logged']) {
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
if (true == $_SESSION['logged'] && ($nbE[1][0] + $nbE[1][1] + $nbE[1][2]) > 0) {
    $nuageSource = $_SESSION['nuage'];
    $stmt = $pdo->prepare('SELECT position FROM membres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $donnees_info2 = $stmt->fetch();
    $positionSource = $donnees_info2['position'];
}

$sql_info = $pdo->query("SELECT id, pseudo, nuage, position, score, lastconnect FROM membres ORDER BY score DESC LIMIT 0,{$nbTop}");
$donnees_info = $sql_info->fetch();
for ($i = 1; $i <= $nbTop; ++$i) {
    echo '<tr>
				<td>'.$i.'</td>
				<td>';

    if ($donnees_info['lastconnect'] > time() - 300) {
        echo ' <a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$donnees_info['pseudo'],' est connect&eacute;</span></a>';
    } else {
        echo ' <a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$donnees_info['pseudo']," n'est pas connect&eacute;</span></a>";
    }

    echo '</td>
				<td>';

    echo $donnees_info['pseudo'].'</td>
				<td>'.formaterNombre(floor($donnees_info['score'] / 1000.)).'</td>
				';

    if (true == $_SESSION['logged']) {
        echo '<td>';
        echo '<a class="bulle" href="',$donnees_info['nuage'],'.nuage.html" >
			<img src="images/nuage.png" title="" alt="" /><span>Nuage : ',$donnees_info['nuage'],'</span></a></td>';
    }

    echo '</tr>';
    $donnees_info = $sql_info->fetch();
}
?>

</table></center>
