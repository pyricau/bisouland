<?php
if (true === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $castToPgBoolean = cast_to_pg_boolean();
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            espion AS keep_gaze_journal
        FROM membres
        WHERE id = :current_account_id
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /**
     * @var array{
     *      keep_gaze_journal: bool,
     * }|false $player
     */
    $player = $stmt->fetch();

    if (isset($_POST['infos'])) {
        $keepGazeJournal = 'on' === ($_POST['keep_gaze_journal'] ?? 'off');
        if ($player['keep_gaze_journal'] !== $keepGazeJournal) {
            $player['keep_gaze_journal'] = $keepGazeJournal;
            $stmt = $pdo->prepare(<<<'SQL'
                UPDATE membres
                SET espion = :keep_gaze_journal
                WHERE id = :current_account_id
            SQL);
            $stmt->execute([
                'keep_gaze_journal' => $castToPgBoolean->from($player['keep_gaze_journal']),
                'current_account_id' => $blContext['account']['id'],
            ]);
        }
    }

    ?>
<br />
<form method="post" action="connected.html">

	<label>
        <input
            type="checkbox"
            <?php if (true === $player['keep_gaze_journal']) { ?>
		    checked="checked"
		    <?php } ?>
            name="keep_gaze_journal"
        />
		Je souhaite enregistrer dans des messages les informations que j'obtiens sur des joueurs.
	</label><br />
	<input type="submit" name="infos" value="Envoyer" />
</form>
<br />
<a href="changepass.html" title="Changer de mot de passe.">Je desire changer de mot de passe.</a><br />
<br />
Si tu en as ras le bol des bisous, tu peux supprimer ton compte !!<br />
<form method="post" action="accueil.html" id="supprime">
	<input type="button" value="Supprimer" onclick="if (confirm('Malheureux, es tu bien sur de vouloir supprimer ton compte ?')) { document.forms.supprime.submit(); } else  { exit; }" />
	<input type="hidden" name="suppr">
</form>
<?php
} else {
    echo 'Erreur : Vous vous croyez ou la ??';
    echo '<br />Veuillez vous connecter.';
}

?>
