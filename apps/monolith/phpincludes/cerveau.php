<?php

use Bl\Domain\KissBlowing\BlownKissState;
use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;

?>
<h1>Cerveau</h1>
<?php

if (true === $blContext['is_signed_in']) {
$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();

$production = calculerGenAmour(
    0,
    3600,
    $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value],
    $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value],
    $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value],
    $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]
);

$stmt = $pdo->prepare(<<<'SQL'
    SELECT score
    FROM membres
    WHERE id = :current_account_id
SQL);
$stmt->execute([
    'current_account_id' => $blContext['account']['id'],
]);
/**
 * @var array{
 *      score: int,
 * }|false $currentPlayer
 */
$currentPlayer = $stmt->fetch();
$score = false !== $currentPlayer ? floor($currentPlayer['score'] / 1000.) : 0;

$stmt = $pdo->prepare(<<<'SQL'
    SELECT COUNT(id) AS players_with_higher_score
    FROM membres
    WHERE score > :current_player_score
SQL);
$stmt->execute([
    'current_player_score' => false !== $currentPlayer ? $currentPlayer['score'] : 0,
]);
/** @var array{players_with_higher_score: int}|false $result */
$result = $stmt->fetch();
$position = false !== $result ? $result['players_with_higher_score'] + 1 : 1;

$stmt = $pdo->query(<<<'SQL'
    SELECT COUNT(id) AS total_players
    FROM membres
    WHERE confirmation = TRUE
SQL);
/** @var array{total_players: int}|false $result */
$result = $stmt->fetch();
$totalJoueur = false !== $result ? $result['total_players'] : 0;

?>
Score : <strong><?php echo formaterNombre($score); ?></strong> Point<?php echo pluriel($score); ?><br />
<br />
Classement : <strong><?php echo $position;
if (1 == $position) {
echo 'er';
} else {
echo 'ème';
}?> / <?php echo $totalJoueur; ?></strong><br />
<br />
R&eacute;serves : <strong><?php echo formaterNombre(floor($amour)); ?></strong> Point<?php echo pluriel(floor($amour)); ?> d'Amour<br />
<br />
Production : <strong><?php echo formaterNombre(floor($production)); ?></strong> Point<?php echo pluriel(floor($production)); ?> d'Amour par heure<br />
<br />
<?php

// On récupère les infos sur le joueur que l'on attaque.
$stmt = $pdo->prepare(<<<'SQL'
    SELECT
        cible AS receiver_account_id,
        finaller,
        finretour,
        butin AS taken_love_points,
        state
    FROM attaque
    WHERE auteur = :current_account_id
SQL);
$stmt->execute([
    'current_account_id' => $blContext['account']['id'],
]);
/**
 * @var array{
 *      receiver_account_id: string, // UUID
 *      finaller: string, // ISO 8601 timestamp string
 *      finretour: string, // ISO 8601 timestamp string
 *      taken_love_points: int,
 *      state: string,
 * }|false $blownKiss
 */
$blownKiss = $stmt->fetch();

if (false !== $blownKiss) {
    $stmt2 = $pdo->prepare(<<<'SQL'
        SELECT
            pseudo,
            nuage,
            position
        FROM membres
        WHERE id = :receiver_account_id
    SQL);
    $stmt2->execute([
        'receiver_account_id' => $blownKiss['receiver_account_id'],
    ]);
    /**
     * @var array{
     *      pseudo: string,
     *      nuage: int,
     *      position: int,
     * }|false $receiver
     */
    $receiver = $stmt2->fetch();
    $finAll = $castToUnixTimestamp->fromPgTimestamptz($blownKiss['finaller']);
    $finRet = $castToUnixTimestamp->fromPgTimestamptz($blownKiss['finretour']);
    $state = BlownKissState::from($blownKiss['state']);

    if (isset($_POST['cancelAttaque']) && BlownKissState::EnRoute === $state) {
        $finRet = (2 * time() + $finRet - 2 * $finAll);
        $stmt3 = $pdo->prepare(<<<'SQL'
            UPDATE attaque
            SET state = 'CalledOff', finretour = :finretour
            WHERE auteur = :current_account_id
        SQL);
        $stmt3->execute([
            'finretour' => $castToPgTimestamptz->fromUnixTimestamp($finRet),
            'current_account_id' => $blContext['account']['id'],
        ]);
        AdminMP(
            $blownKiss['receiver_account_id'],
            'Attaque annulée',
            "{$pseudo} a annulé son attaque.\n"
            ."Tu n'es plus en danger.",
        );
        $state = BlownKissState::CalledOff; // Update local variable to reflect the change
    }

    if (BlownKissState::EnRoute === $state) {
?>
Tu vas tenter d'embrasser <strong><?php echo $receiver['pseudo']; ?></strong>
 sur le nuage <strong><?php echo $receiver['nuage']; ?></strong>
 &agrave; la position <strong><?php echo $receiver['position']; ?></strong>.<br /><br />
Tes bisous atteindront <strong><?php echo $receiver['pseudo']; ?></strong> dans :
	<script src="includes/compteur.js" type="text/javascript"></script>
	<span id="compteur"><?php echo strTemps($finAll - time()); ?></span>
	<script language="JavaScript">
		duree="<?php echo $finAll - time(); ?>";
		stop="";
		fin="";
		next="Termin&eacute;";
		adresseStop="";
		adresseFin="cerveau.html";

		duree2="<?php echo $finRet - time(); ?>";
		stop2="";
		fin2="";
		next2="Termin&eacute;";
		adresseStop2="";
		adresseFin2="cerveau.html";

		nbCompteur=2;

		t();
	</script>
<br />
<br />
Ils seront de retour dans : <span id="compteur2"><?php echo strTemps($finRet - time()); ?></span>
<br />
<br />
<form method="post" action="cerveau.html">
	<input type="submit" name="cancelAttaque" value="Annuler l'attaque" />
</form>
<?php
    } else {
?>
Tes bisous ont tent&eacute; d'embrasser <strong><?php echo $receiver['pseudo']; ?></strong>
 sur le nuage <strong><?php echo $receiver['nuage']; ?></strong>
 &agrave; la position <strong><?php echo $receiver['position']; ?></strong>.<br /><br />
Ils seront de retour dans :
	<script src="includes/compteur.js" type="text/javascript"></script>
	<span id="compteur"><?php echo strTemps($finRet - time()); ?></span>
	<script language="JavaScript">
		duree="<?php echo $finRet - time(); ?>";
		stop="";
		fin="";
		next="Termin&eacute;";
		adresseStop="";
		adresseFin="cerveau.html";
		nbCompteur=1;
		t();
	</script>
<br />
Ils ont pris &agrave; <strong><?php echo $receiver['pseudo']; ?></strong> <strong><?php echo formaterNombre($blownKiss['taken_love_points']); ?></strong> Points d'Amour.
<?php
    }
}
// Infos sur les joueurs qui nous attaquent.
$stmt = $pdo->prepare(<<<'SQL'
    SELECT
        auteur AS sender_account_id,
        finaller
    FROM attaque
    WHERE (
        cible = :current_account_id
        AND state = 'EnRoute'
    )
    ORDER BY finaller
SQL);
$stmt->execute([
    'current_account_id' => $blContext['account']['id'],
]);
/**
 * @var array<int, array{
 *      sender_account_id: string, // UUID
 *      finaller: string, // ISO 8601 timestamp string
 * }> $incomingKisses
 */
$incomingKisses = $stmt->fetchAll();

foreach ($incomingKisses as $incomingKiss) {
    $stmt2 = $pdo->prepare(<<<'SQL'
        SELECT
            pseudo,
            nuage,
            position
        FROM membres
        WHERE id = :sender_account_id
    SQL);
    $stmt2->execute([
        'sender_account_id' => $incomingKiss['sender_account_id'],
    ]);
    /**
     * @var array{
     *      pseudo: string,
     *      nuage: int,
     *      position: int,
     * }|false $sender
     */
    $sender = $stmt2->fetch();
    $finAll = $castToUnixTimestamp->fromPgTimestamptz($incomingKiss['finaller']);

?>
Pr&eacute;pare toi : <strong><?php echo $sender['pseudo']; ?></strong> va essayer de t'embrasser
 depuis le nuage <strong><?php echo $sender['nuage']; ?></strong>,
 &agrave; la position <strong><?php echo $sender['position']; ?></strong>,
 dans <strong><?php echo strTemps($finAll - time()); ?></strong>.<br />


<?php
}// Fin du foreach
}// Fin du login true
else {
    echo 'Tu n\'es pas connecté !!';
}
?>
