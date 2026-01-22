<?php

use Symfony\Component\Uid\Uuid;

if (false === $blContext['is_signed_in']) {
    $pdo = bd_connect();
    $send = 0;
    $pseudo = '';
    $mdp = '';
    if (isset($_POST['inscription'])) {
        // Mesure de securite.
        $pseudo = htmlentities((string) $_POST['Ipseudo']);
        $mdp = htmlentities((string) $_POST['Imdp']);
        $mdp2 = htmlentities((string) $_POST['Imdp2']);
        // Prevoir empecher de prendre un pseudo deje existant
        // Si les variables contenant le pseudo, le mot de passe existent et contiennent quelque chose.
        if (isset($_POST['Ipseudo'], $_POST['Imdp'], $_POST['Imdp2']) && !empty($_POST['Ipseudo']) && !empty($_POST['Imdp']) && !empty($_POST['Imdp2'])) {
            if ($mdp === $mdp2) {
                // Si le pseudo est superieur e 3 caracteres et inferieur e 35 caracteres.
                $taille = strlen(trim((string) $_POST['Ipseudo']));
                if ($taille >= 4 && $taille <= 15) {
                    /* //Mesure de securite.
                    $pseudo = htmlentities(addslashes($_POST['pseudo']));
                    $mdp = htmlentities(addslashes($_POST['mdp']));*/

                    // Verifie si le pseudo n'est pas deje pris.
                    $stmt = $pdo->prepare('SELECT id FROM membres WHERE pseudo = :pseudo LIMIT 1');
                    $stmt->execute(['pseudo' => $pseudo]);

                    if (
                        false === $stmt->fetch()
                        && 'BisouLand' !== $pseudo
                    ) {
                        // Verifie que le pseudo est correct.
                        if (preg_match("!^\w+$!", $pseudo)) {
                            if (preg_match("!^\w+$!", $mdp)) {
                                // Si le mot de passe est superieur e 4 caracteres.
                                $taille = strlen(trim((string) $_POST['Imdp']));
                                if ($taille >= 5 && $taille <= 15) {
                                    // On execute la requete qui enregistre un nouveau membre.

                                    // Hashage du mot de passe avec Bcrypt ou Argon2.
                                    $hmdp = password_hash($mdp, \PASSWORD_DEFAULT);

                                    $id = Uuid::v7();
                                    $stmt = $pdo->prepare(
                                        'INSERT INTO membres (id, pseudo, mdp, timestamp, lastconnect, amour)'
                                        .' VALUES (:id, :pseudo, :mdp, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :amour)',
                                    );
                                    $stmt->execute([
                                        'id' => $id,
                                        'pseudo' => $pseudo,
                                        'mdp' => $hmdp,
                                        'amour' => 300,
                                    ]);

                                    GiveNewPosition($id);

                                    sendNotification(
                                        $id,
                                        'Bienvenue sur BisouLand',
                                        <<<'TXT'
                                        Merci pour l'intérêt que tu portes à BisouLand.
                                        Il est probable que certaines choses te paraissent obscures pour le moment.
                                        Pense à faire un tour sur la page Aide, puis sur la page Encyclopédie, pour découvrir comment fonctionne BisouLand.
                                        En haut à droite se trouve le menu de jeu, c'est ici que tu pourras gérer ton compte BisouLand.
                                        Si tu as des questions, n'hésite pas à envoyer un message privé à l'admin.

                                        Amicalement, et avec plein de Bisous
                                        L'équipe BisouLand
                                        TXT,
                                    );

                                    echo 'Ton inscription est confirmée ! Tu peux maintenant te connecter.<br />';
                                    $send = 1;
                                } else {
                                    echo 'Erreur : le mot de passe est soit trop court, soit trop long !';
                                }
                            } else {
                                echo "Erreur : le mot de passe n'est pas valide !";
                            }
                        } else {
                            echo "Erreur : le pseudo n'est pas valide !";
                        }
                    } else {
                        echo 'Erreur : pseudo deje pris !';
                    }
                } else {
                    echo 'Erreur : le pseudo est soit trop court, soit trop long !';
                }
            } else {
                echo "Erreur : Tu n'as pas rentre deux fois le meme mot de passe !";
            }
        } else {
            echo 'Erreur : Pense e remplir tous les champs !';
        }
    }
    if (0 === $send) {
        ?>
<form method="post" class="formul" action="inscription.html">
	<label>Pseudo :<br /><span class="petit">(Entre 4 et 15 caracteres)</span><br /><input type="text" name="Ipseudo" tabindex="10" size="15" maxlength="15" value="<?php echo stripslashes($pseudo); ?>"/></label><br />
	<label>Mot de passe : <br /><span class="petit">(Entre 5 et 15 caracteres)</span><br /><input type="password" name="Imdp" tabindex="20" size="15" maxlength="15" value=""/></label><br />
	<label>Reecris le mot de passe : <br /><input type="password" name="Imdp2" tabindex="30" size="15" maxlength="15" value=""/></label><br />
    <input type="submit" name="inscription" value="S'inscrire" />
</form>
<?php
    }
} else {
    echo "Pfiou t'es dja connected toi !!";
}
?>
