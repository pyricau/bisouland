<?php
$errorMessage = match ($_GET['e'] ?? '') {
    '1' => 'Erreur : le mot de passe est incorrect !',
    '2' => "Erreur : le pseudo n'existe pas !",
    '3' => 'Erreur : vous avez oublié de remplir un ou plusieurs champs !',
    '4' => 'Erreur : vous devez être connecté pour vous déconnecter !',
    default => null,
};
?>

<h1>Connexion</h1>
<?php if (false === $blContext['is_signed_in']) { ?>
    <?php if (null !== $errorMessage) { ?>
        <br /><?php echo $errorMessage; ?><br /><br />
    <?php } ?>
    <form method="post" class="formul" action="connexion.html">
        <label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caractères)</span><br /><input type="text" name="pseudo" tabindex="10" size="15" maxlength="15" value=""/></label><br />
        <label>Mot de passe : <br /><span class="petit">(Entre 5 et 15 caractères)</span><br /><input type="password" name="mdp" tabindex="20" size="15" maxlength="15" value=""/></label><br />
        <label><input type="checkbox" checked="checked" name="auto" />Connexion automatique</label><br />
        <input type="submit" name="connexion" value="Se connecter" /><br /><br />
    </form>
<?php } else { ?>
    T'es dja connected !!
<?php } ?>
