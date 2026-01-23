<?php
if (true === $blContext['is_signed_in']) {
    ?>
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
