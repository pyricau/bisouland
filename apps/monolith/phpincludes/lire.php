<?php

if (true === $_SESSION['logged']) {
    $pdo = bd_connect();

    if (isset($_GET['idmsg']) && !empty($_GET['idmsg'])) {
        $idmsg = htmlentities((string) $_GET['idmsg']);
        $stmt = $pdo->prepare('SELECT posteur, destin, message, timestamp, statut, titre FROM messages WHERE id = :id');
        $stmt->execute(['id' => $idmsg]);
        $donnees = $stmt->fetch();
        if ($donnees['destin'] == $_SESSION['id']) {
            if (false === $donnees['statut']) {
                $stmt2 = $pdo->prepare('UPDATE messages SET statut = TRUE WHERE id = :id');
                $stmt2->execute(['id' => $idmsg]);
            }
            $stmt = $pdo->prepare('SELECT pseudo FROM membres WHERE id = :id');
            $stmt->execute(['id' => $donnees['posteur']]);
            $donnees2 = $stmt->fetch();
            $from = $donnees2['pseudo'];

            $objet = $donnees['titre'];
            $message = $donnees['message'];
            $dateEnvoie = $donnees['timestamp'];
            ?>

<a href="boite.html" title="Messages">Retour à la liste des messages</a>
<br />
<p>Auteur : <?php echo stripslashes((string) $from); ?></p>
<p>Envoyé le <?php echo date('d/m/Y à H\hi', $dateEnvoie); ?></p>
<p>Objet : <?php echo stripslashes((string) $objet); ?></p>
Message :<br />
<div class="message"><?php echo bbLow($message); ?></div>
<form method="post" action="boite.html">
	<input type="submit" tabindex="30" value="Supprimer" />
	<input type="hidden" name="supprimer" value="<?php echo $idmsg; ?>" />
</form>

<?php
        } else {
            echo "Tu n'as pas le droit de visionner ce message !!";
        }
    } else {
        echo 'Pas d\'id message spécifiée !!';
    }
} else {
    echo 'Tu n\'es pas connecté !!';
}
?>
