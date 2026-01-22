<?php

if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToUnixTimestamp = cast_to_unix_timestamp();

    if (isset($_GET['notification_id']) && !empty($_GET['notification_id'])) {
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE notifications
            SET has_been_read = TRUE
            WHERE (
                notification_id = :notification_id
                AND account_id = :account_id
            )
            RETURNING notification_id, title, message, received_at
        SQL);
        $stmt->execute([
            'notification_id' => $_GET['notification_id'],
            'account_id' => $blContext['account']['id'],
        ]);
        /**
         * @var array{
         *     notification_id: string, // UUID
         *     title: string,
         *     message: string,
         *     received_at: string, // ISO 8601 timestamp string
         * }|false $notification
         */
        $notification = $stmt->fetch();
        if (false !== $notification) {
            ?>

<a href="boite.html" title="Notifications">Retour à la liste des notifications</a>
<br />
<h2><?php echo htmlspecialchars($notification['title']); ?></h2>
<p>Envoyé le <?php echo date('d/m/Y à H\hi', $castToUnixTimestamp->fromPgTimestamptz($notification['received_at'])); ?></p>
<div class="message"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></div>
<form method="post" action="boite.html">
	<input type="submit" tabindex="30" value="Supprimer" />
	<input type="hidden" name="supprimer" value="<?php echo $notification['notification_id']; ?>" />
</form>

        <?php } else { ?>
            Tu n'as pas le droit de visionner cette notification !!
        <?php } ?>
    <?php } else { ?>
        Pas d'id notification spécifiée !!
    <?php } ?>
<?php } else { ?>
    Tu n'es pas connecté !!
<?php } ?>
