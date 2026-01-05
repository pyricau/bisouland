<?php

if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();

    if (isset($_GET['idmsg']) && !empty($_GET['idmsg'])) {
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                posteur AS sender_account_id,
                destin AS receiver_account_id,
                message AS content,
                timestamp,
                statut AS has_been_read,
                titre
            FROM messages
            WHERE id = :message_id
        SQL);
        $stmt->execute([
            'message_id' => $_GET['idmsg'],
        ]);
        /**
         * @var array{
         *     sender_account_id: string, // UUID
         *     receiver_account_id: string, // UUID
         *     content: string,
         *     timestamp: string, // ISO 8601 timestamp string
         *     has_been_read: bool,
         *     titre: string,
         * }|false $message
         */
        $message = $stmt->fetch();
        if (
            false !== $message
            && $message['receiver_account_id'] === $blContext['account']['id']
        ) {
            if (false === $message['has_been_read']) {
                $stmt = $pdo->prepare(<<<'SQL'
                    UPDATE messages
                    SET statut = TRUE
                    WHERE id = :message_id
                SQL);
                $stmt->execute([
                    'message_id' => $_GET['idmsg'],
                ]);
            }
            $stmt = $pdo->prepare(<<<'SQL'
                SELECT pseudo
                FROM membres
                WHERE id = :sender_account_id
            SQL);
            $stmt->execute([
                'sender_account_id' => $message['sender_account_id'],
            ]);
            /**
             * @var array{
             *     pseudo: string,
             * }|false $sender
             */
            $sender = $stmt->fetch();
            $from = false !== $sender ? $sender['pseudo'] : '';
            ?>

<a href="boite.html" title="Messages">Retour à la liste des messages</a>
<br />
<p>Auteur : <?php echo $from; ?></p>
<p>Envoyé le <?php echo date('d/m/Y à H\hi', $castToUnixTimestamp->fromPgTimestamptz($message['timestamp'])); ?></p>
<p>Objet : <?php echo stripslashes((string) $message['titre']); ?></p>
Message :<br />
<div class="message"><?php echo bbLow($message['content']); ?></div>
<form method="post" action="boite.html">
	<input type="submit" tabindex="30" value="Supprimer" />
	<input type="hidden" name="supprimer" value="<?php echo htmlentities($_GET['idmsg']); ?>" />
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
