<?php

// --------------- Etape 1 -----------------
// Si un message est envoyé, on l'enregistre
// -----------------------------------------

$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();

if (isset($_POST['message'])) {
    if (true === $blContext['is_signed_in']) {
        $psd = htmlentities((string) $blContext['account']['pseudo']);

        $message = htmlentities((string) $_POST['message'], \ENT_QUOTES); // De même pour le message
        $message = nl2br($message); // Pour le message, comme on utilise un textarea, il faut remplacer les Entrées par des <br />

        // On peut enfin enregistrer :o)
        // $stmt = $pdo->prepare("INSERT INTO orbisous (pseudo, message, timestamp) VALUES(?, ?, ?)");
        // $stmt->execute([$psd, $message, time()]);
    }
} else {
    $psd = 'Votre pseudo';
}

// --------------- Etape 2 -----------------
// On écrit les liens vers chacune des pages
// -----------------------------------------

// On met dans une variable le nombre de messages qu'on veut par page
$nombreDeMessagesParPage = 5; // Essayez de changer ce nombre pour voir :o)

// On récupère le nombre total de messages
$retour = $pdo->query('SELECT COUNT(*) AS nb_messages FROM orbisous');
$donnees = $retour->fetch();
$totalDesMessages = $donnees['nb_messages'];

// On calcule le nombre de pages à créer
$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);
?>

<h1>Livre d'or</h1>

<?php if (true === $blContext['is_signed_in']) { ?>
<div class=formul>
<form method="post" action="livreor.html">
    <p>Le livre d'or a été désactivé, en vue du passage à la v2. Vous pourrez de nouveau poster des messages dans le livre d'or
	dès que la version 2 de BisouLand sera lancée.<br /> <br />
	En attendant, vous pouvez visiter le Livre d'Or de la version 2 ici : <a href="livre_or.html">Nouveau Livre d'Or</a></p>
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
<?php
    }
?>


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

$reponse = $pdo->prepare('SELECT * FROM orbisous ORDER BY id DESC LIMIT :limit OFFSET :offset');
$reponse->execute(['limit' => $nombreDeMessagesParPage, 'offset' => (int) $premierMessageAafficher]);

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

<?php
while ($donnees = $reponse->fetch()) {
    ?>
<div class=livreor>
<?php
        echo '<p><strong>'.stripslashes((string) $donnees['pseudo']).'</strong> a &eacute;crit le '.date('d/m/Y à H\hi', $castToUnixTimestamp->fromPgTimestamptz($donnees['timestamp'])).' :<br /><br />'.stripslashes((string) $donnees['message']).'</p>';
    ?>
</div>
<?php
}
?>
