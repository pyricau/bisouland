<?php

session_start();
include __DIR__ . '/phpincludes/bd.php';
$pdo = bd_connect();

if (isset($_POST['connexion'])) {
    // Ensuite on vérifie que les variables existent et contiennent quelque chose :)
    if (isset($_POST['pseudo'], $_POST['mdp']) && !empty($_POST['pseudo']) && !empty($_POST['mdp'])) {
        // Mesure de sécurité, notamment pour éviter les injections sql.
        // Le htmlentities évitera de le passer par la suite.
        $pseudo = htmlentities((string) $_POST['pseudo']);
        $mdp = htmlentities((string) $_POST['mdp']);
        // Hashage du mot de passe.
        $mdp = md5($mdp);

        // La requête qui compte le nombre de pseudos
        $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo = :pseudo');
        $stmt->execute(['pseudo' => $pseudo]);

        // La on vérifie si le nombre est différent que zéro
        if (0 != $stmt->fetchColumn()) {
            // Sélection des informations.
            $stmt = $pdo->prepare('SELECT id, confirmation, mdp, nuage FROM membres WHERE pseudo = :pseudo');
            $stmt->execute(['pseudo' => $pseudo]);
            $donnees_info = $stmt->fetch();

            // Si le mot de passe est le même.
            if ($donnees_info['mdp'] == $mdp) {
                // Si le compte est confirmé.
                if (1 == $donnees_info['confirmation']) {
                    // On modifie la variable qui nous indique que le membre est connecté.
                    $_SESSION['logged'] = true;

                    // On créé les variables contenant des informations sur le membre.
                    $_SESSION['id'] = $donnees_info['id'];
                    $_SESSION['pseudo'] = $pseudo;
                    $_SESSION['nuage'] = $donnees_info['nuage'];

                    if (isset($_POST['auto'])) {
                        $timestamp_expire = time() + 30 * 24 * 3600;
                        setcookie('pseudo', $pseudo, ['expires' => $timestamp_expire]);
                        setcookie('mdp', $mdp, ['expires' => $timestamp_expire]);
                    }

                    // On supprime le membre non connecté du nombre de visiteurs :
                    $stmt = $pdo->prepare('DELETE FROM connectbisous WHERE ip = :ip');
                    $stmt->execute(['ip' => $_SERVER['REMOTE_ADDR']]);

                    // On redirige le membre.
                    header('location: cerveau.html');
                } else {
                    $_SESSION['errCon'] = 'Erreur : le compte n\'est pas confirmé !';
                    $_SESSION['logged'] = false;
                    header('location: connexion.html');
                }
            } else {
                $_SESSION['errCon'] = 'Erreur : le mot de passe est incorrect !';
                $_SESSION['logged'] = false;
                header('location: connexion.html');
            }
        } else {
            $_SESSION['errCon'] = "Erreur : le pseudo n'existe pas !";
            $_SESSION['logged'] = false;
            header('location: connexion.html');
        }
    } else {
        $_SESSION['errCon'] = 'Erreur : vous avez oublié de remplir un ou plusieurs champs !';
        $_SESSION['logged'] = false;
        header('location: connexion.html');
    }
} else {
    $_SESSION['errCon'] = 'Erreur : Vous n\'avez pas acces à cette page !';
    $_SESSION['logged'] = false;
    header('location: connexion.html');
}
