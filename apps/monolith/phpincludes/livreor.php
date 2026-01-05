<?php

// --------------- Etape 1 -----------------
// Si un message est envoyé, on l'enregistre
// -----------------------------------------

$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();

if (isset($_POST['message']) && true === $blContext['is_signed_in']) {
    // On peut enfin enregistrer :o)
    // temporarily disabled
    // $stmt = $pdo->prepare(<<<SQL
    //     INSERT INTO orbisous (pseudo, message, timestamp)
    //     VALUES(?, ?, CURRENT_TIMESTAMP)
    // SQL);
    // $stmt->execute([
    //     $blContext['account']['pseudo'],
    //     nl2br(htmlentities($_POST['message'], \ENT_QUOTES)),
    // ]);
}

// --------------- Etape 2 -----------------
// On écrit les liens vers chacune des pages
// -----------------------------------------

// On met dans une variable le nombre de messages qu'on veut par page
$nombreDeMessagesParPage = 5; // Essayez de changer ce nombre pour voir :o)

// On récupère le nombre total de messages
$stmt = $pdo->query(<<<'SQL'
    SELECT COUNT(id) AS total_messages
    FROM orbisous
SQL);
/** @var array{total_messages: int}|false $result */
$result = $stmt->fetch();
$totalDesMessages = false !== $result ? $result['total_messages'] : 0;

// On calcule le nombre de pages à créer
$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);
?>

<h1>Livre d'or</h1>

<?php if (true === $blContext['is_signed_in']) { ?>
<div class=formul>
<form method="post" action="livreor.html">
    <p>Le livre d'or a été désactivé, en vue du passage à la v2. Vous pourrez de nouveau poster des messages dans le livre d'or
	dès que la version 2 de BisouLand sera lancée.<br /> <br />
   <?php
           /*
            <p>
                <label>Message :<br />
                <textarea name="message" tabindex="20" rows="8" cols="35">
        Entrez ici votre commentaire.</textarea> <br /></label>
                <input type="submit" tabindex="30" value="Envoyer" />
            </p> */
    ?>
</form>
</div>
<?php } ?>

<p>

<center>Il y a actuellement <strong><?php echo $totalDesMessages; ?></strong> messages dans le livre d'or.</center>
<br />
<?php

if (isset($_GET['or'])) {
    $or = (int) $_GET['or']; // On récupère le numéro de la page indiqué dans l'adresse (livreor.php?page=4)
    if ($or > $nombreDePages) {
        $or = $nombreDePages;
    } elseif ($or < 1) {
        $or = 1;
    }
} else { // La variable n'existe pas, c'est la première fois qu'on charge la page
    $or = 1; // On se met sur la page 1 (par défaut)
}

// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premierMessageAafficher = ($or - 1) * $nombreDeMessagesParPage;

$stmt = $pdo->prepare(<<<'SQL'
    SELECT
        id,
        pseudo,
        message,
        timestamp
    FROM orbisous
    ORDER BY id DESC
    LIMIT :messages_per_page
    OFFSET :first_message_offset
SQL);
$stmt->execute([
    'messages_per_page' => $nombreDeMessagesParPage,
    'first_message_offset' => (int) $premierMessageAafficher,
]);
/**
 * @var array<int, array{
 *     id: string, // UUID
 *     pseudo: string,
 *     message: string,
 *     timestamp: string, // ISO 8601 timestamp string
 * }> $guestbookEntries
 */
$guestbookEntries = $stmt->fetchAll();

if ($nombreDePages > 1) {
    echo '<center>Page :';
    for ($i = 1; $i <= $nombreDePages; ++$i) {
        if ($i != $or) {
            echo '<a href="livreor.'.$i.'.html">'.$i.'</a> ';
        } else {
            echo ' '.$i.' ';
        }
    }
    echo '</center><br />';
}
?>

</p>

<?php foreach ($guestbookEntries as $guestbookEntry) { ?>
<div class=livreor>
<?php
        echo '<p><strong>'.stripslashes((string) $guestbookEntry['pseudo']).'</strong> a &eacute;crit le '.date('d/m/Y à H\hi', $castToUnixTimestamp->fromPgTimestamptz($guestbookEntry['timestamp'])).' :<br /><br />'.stripslashes((string) $guestbookEntry['message']).'</p>';
    ?>
</div>
<?php } ?>
