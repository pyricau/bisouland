<?php
// Ce qu'on affiche si on est connecte
if (true === $_SESSION['logged']) {
    $pdo = bd_connect();
    $castToPgBoolean = cast_to_pg_boolean();
    $stmt = $pdo->prepare('SELECT espion FROM membres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $donnees_info = $stmt->fetch();
    $espion = $donnees_info['espion'];

    if (isset($_POST['infos'])) {
        $esp = isset($_POST['espion']);
        if ($espion !== $esp) {
            $espion = $esp;
            $stmt = $pdo->prepare('UPDATE membres SET espion = :espion WHERE id = :id');
            $stmt->execute(['espion' => $castToPgBoolean->from($espion), 'id' => $id]);
        }
    }

    ?>
<br />
<form method="post" action="connected.html">

	<label>
		<input type="checkbox" <?php if (true === $espion) {
            echo 'checked="checked"';
        } ?> name="espion" />
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
