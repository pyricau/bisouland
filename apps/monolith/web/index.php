<?php

header('Content-type: text/html; charset=UTF-8');

// Next step :
// dents acérées --> arracher langue.
// Remplacer bouton annuler par lien annuler au niveau du compteur.
// attaques
// Scores
// Permettre de se déplacer, moyennant des points.

// Attaque : mettre en place la possibilité d'attaquer, avec choix etc..
// Créer système de MP automatique pour avertir.

// Démarrage de la session
session_start();
ob_start();

include __DIR__.'/phpincludes/bd.php';
$pdo = bd_connect();

include __DIR__.'/phpincludes/fctIndex.php';

$inMainPage = true;

// Front Controller: Handle POST requests
// Handle login
if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['connexion'])) {
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
                    exit;
                }
                $_SESSION['errCon'] = 'Erreur : le compte n\'est pas confirmé !';
                $_SESSION['logged'] = false;
                header('location: connexion.html');
                exit;
            }
            $_SESSION['errCon'] = 'Erreur : le mot de passe est incorrect !';
            $_SESSION['logged'] = false;
            header('location: connexion.html');
            exit;
        }
        $_SESSION['errCon'] = "Erreur : le pseudo n'existe pas !";
        $_SESSION['logged'] = false;
        header('location: connexion.html');
        exit;
    }
    $_SESSION['errCon'] = 'Erreur : vous avez oublié de remplir un ou plusieurs champs !';
    $_SESSION['logged'] = false;
    header('location: connexion.html');
    exit;
}

// Front Controller: Handle logout (via GET parameter)
$page = (empty($_GET['page'])) ? 'accueil' : htmlentities((string) $_GET['page']);
if ('logout' === $page) {
    // Ensuite on vérifie que la variable $_SESSION['logged'] existe et vaut bien true.
    if (isset($_SESSION['logged']) && true == $_SESSION['logged']) {
        $timeDeco = time() - 600;
        $stmt = $pdo->prepare('UPDATE membres SET lastconnect = :lastconnect WHERE id = :id');
        $stmt->execute(['lastconnect' => $timeDeco, 'id' => $_SESSION['id']]);
        // On modifie la valeur de $_SESSION['logged'], qui devient false.
        $_SESSION['logged'] = false;
        $timestamp_expire = time() - 1000;
        setcookie('pseudo', '', ['expires' => $timestamp_expire]);
        setcookie('mdp', '', ['expires' => $timestamp_expire]);

        // Redirection.
        header('location: accueil.html');
        exit;
    }
    $_SESSION['errCon'] = 'Erreur : vous devez être connecté pour vous déconnecter !';
    $_SESSION['logged'] = false;
    header('location: connexion.html');
    exit;
}

// Mesures de temps pour évaluer le temps que met la page a se créer.
$temps_debut = microtime_float();

// Si la variable $_SESSION['logged'] n'existe pas, on la créée, et on l'initialise a false
if (!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
}

// Note: $page is already set above in the logout handler (line 103)

// Test en cas de suppression de compte
// Il faudra a jouter ici une routine de suppression des messages dans la bdd.
// Ainsi que des constructions en cours, etc..
if (isset($_POST['suppr']) && true == $_SESSION['logged']) {
    $_SESSION['pseudo'] = 'Not Connected';
    $_SESSION['logged'] = false;
    SupprimerCompte($_SESSION['id']);
}

// Si on est pas connecté.
if (false == $_SESSION['logged']) {
    $id = 0;
    // On récupère les cookies enregistrés chez l'utilisateurs, s'ils sont la.
    if (isset($_COOKIE['pseudo']) && isset($_COOKIE['mdp'])) {
        $pseudo = htmlentities(addslashes((string) $_COOKIE['pseudo']));
        $mdp = htmlentities(addslashes($_COOKIE['mdp']));
        // La requête qui compte le nombre de pseudos
        $stmt = $pdo->prepare('SELECT COUNT(*) AS nb_pseudo FROM membres WHERE pseudo = :pseudo');
        $stmt->execute(['pseudo' => $pseudo]);

        if (0 != $stmt->fetchColumn()) {
            // Sélection des informations.
            $stmt = $pdo->prepare('SELECT id, confirmation, mdp, nuage FROM membres WHERE pseudo = :pseudo');
            $stmt->execute(['pseudo' => $pseudo]);
            $donnees_info = $stmt->fetch();

            // Si le mot de passe est le même (le mot de passe est déjà crypté).
            // Si le compte est confirmé.
            if ($donnees_info['mdp'] == $mdp && 1 == $donnees_info['confirmation']) {
                // On modifie la variable qui nous indique que le membre est connecté.
                $_SESSION['logged'] = true;
                // On créé les variables contenant des informations sur le membre.
                $_SESSION['id'] = $donnees_info['id'];
                $_SESSION['pseudo'] = $pseudo;
                $_SESSION['nuage'] = $donnees_info['nuage'];
                $page = 'cerveau';
            }
        }
    }
} else {
    $pseudo = $_SESSION['pseudo'];
}

$temps11 = microtime_float();

// Informations nécessaires au fonctionnement du jeu.

$nbType = [
    6,
    3,
    5,
];

$Obj[0] = [
    'coeur',
    'bouche',
    'langue',
    'dent',
    'jambes',
    'oeil',
];

$Obj[1] = [
    'smack',
    'baiser',
    'pelle',
];

$Obj[2] = [
    'tech1',
    'tech2',
    'tech3',
    'tech4',
    'soupe',
];

// ***************************************************************************
// Si on est connecté
if (true == $_SESSION['logged']) {
    // l'id du membre.
    $id = $_SESSION['id'];

    // Fonction destinée à l'administration
    if (isset($_POST['UnAct']) && 12 == $id) {
        actionAdmin();
    }

    $stmt = $pdo->prepare('SELECT timestamp, coeur, bouche, amour, jambes, smack, baiser, pelle, tech1, tech2, tech3, tech4, dent, langue, bloque, soupe, oeil FROM membres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $donnees_info = $stmt->fetch();
    // Date du dernier calcul du nombre de points d'amour.
    $lastTime = $donnees_info['timestamp'];
    // Temps écoulé depuis le dernier calcul.
    $timeDiff = time() - $lastTime;

    // On récupère le nombre de points d'amour.
    $amour = $donnees_info['amour'];

    $joueurBloque = $donnees_info['bloque'];

    // Nombre d'objets d'un type donné.
    // Batiments

    $nbE = [];

    for ($i = 0; $i < 3; ++$i) {
        for ($j = 0; $j < $nbType[$i]; ++$j) {
            $nbE[$i][$j] = $donnees_info[$Obj[$i][$j]];
        }
    }

    // Cout en point d'amour pour la construction d'un objet
    // Organes
    $amourE[0] = [
        expo(100, 0.4, $nbE[0][0], 1),
        expo(200, 0.4, $nbE[0][1], 1),
        expo(250, 0.4, $nbE[0][2], 1),
        expo(500, 0.4, $nbE[0][3], 1),
        expo(1000, 0.6, $nbE[0][4], 1),
        expo(1000, 0.4, $nbE[0][5], 1),
    ];

    // Bisous
    $amourE[1] = [
        800,
        3500,
        10000,
    ];

    // Technos
    $amourE[2] = [
        expo(1000, 0.4, $nbE[2][0], 1),
        expo(2000, 0.4, $nbE[2][1], 1),
        expo(3000, 0.4, $nbE[2][2], 1),
        expo(10000, 0.6, $nbE[2][3], 1),
        expo(5000, 0.4, $nbE[2][4], 1),
    ];

    // Temps pour la construction de l'objet.
    // Organes
    $tempsE[0] = [
        ExpoSeuil(235000, 20, $nbE[0][0] - $nbE[2][4], 1),
        ExpoSeuil(200000, 25, $nbE[0][1] - $nbE[2][4], 1),
        ExpoSeuil(220000, 22, $nbE[0][2] - $nbE[2][4], 1),
        ExpoSeuil(210000, 17, $nbE[0][3] - $nbE[2][4], 1),
        ExpoSeuil(1000000, 5, $nbE[0][4] - $nbE[2][4], 1),
        ExpoSeuil(500000, 5, $nbE[0][5] - $nbE[2][4], 1),
    ];

    // Bisous
    $tempsE[1] = [
        InvExpo(100, 1.5, $nbE[0][1], 1),
        InvExpo(250, 1.7, $nbE[0][1], 1),
        InvExpo(500, 2, $nbE[0][1], 1),
    ];

    // Tech
    $tempsE[2] = [
        expo(50, 0.4, $nbE[2][0] - $nbE[2][4], 1),
        expo(1000, 0.4, $nbE[2][1] - $nbE[2][4], 1),
        expo(3000, 0.4, $nbE[2][2] - $nbE[2][4], 1),
        expo(15000, 0.6, $nbE[2][3] - $nbE[2][4], 1),
        expo(5000, 0.3, $nbE[2][4], 1),
    ];

    $amour = calculterAmour($amour, $timeDiff, $nbE[0][0], $nbE[1][0], $nbE[1][1], $nbE[1][2]);
    // Mise a jour du nombre de points d'amour, par rapport au temps écoulé.

    // Gestion des pages d'évolution (constructions).
    $evolPage = -1; // Valeur par défaut.
    if ('construction' === $page) {
        $evolPage = 0;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Coeur',
            'Bouche',
            'Langue',
            'Dents',
            'Jambes',
            'Yeux',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Le coeur permet de produire encore et toujours plein d\'amour.<br />
				<span class="info">[ Plus le niveau de Coeur est &eacute;lev&eacute;, plus les Points d\'Amours augmentent rapidement ]<br />
				[ Augmente un peu la distance maximale d\'attaque ]<br />
				[ Augmente le nombre de Points d\'amour d&eacute;pens&eacute;s lors d\'un saut ]</span><br />',

            'La bouche permet de donner des baisers, de plus en plus fougueux.<br />
				<span class="info">[ Acc&eacute;lère la cr&eacute;ation des Bisous ]<br />
				[ Augmente la force des Bisous ]</span><br />',

            'La langue permet de cr&eacute;er de nouveaux bisous, plus efficaces.<br />
				<span class="info">[ Augmente la force des Baisers langoureux ]</span><br />',

            'Avec des dents bien aiguis&eacute;es, personne n\'osera vous approcher.<br />
				<span class="info">[ Augmente la d&eacute;fense lors d\'une agression ]<br />
				[ Augmente les chances de d&eacute;truire des Baisers langoureux ]</span><br />
				',

            'Balade toi dans les nuages !!<br />
				<span class="info">
				[ Augmente beaucoup la distance maximale pour embrasser ]<br />
				[ Augmente un peu la distance maximale de saut ]<br />
				[ Diminue le temps n&eacute;cessaire pour embrasser ]<br />
				[ Diminue le nombre de Points d\'Amour n&eacute;cessaires pour embrasser ]</span><br />',

            'Elle a les yeux r&eacute;volver...<br />
				<span class="info">[ Permet d\'obtenir des informations sur un joueur ]
				[ Chaque niveau augmente les chances d\'obtenir plus d\'information sur un joueur ]<br />
				[ Chaque niveau diminue les chances d\'obtenir plus d\'information sur vous ]</span><br />',
        ];
    } elseif ('bisous' === $page) {
        $evolPage = 1;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Smacks',
            'Baisers',
            'Baisers langoureux',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Pour tenter une première approche.<br />
				<span class="info">[ Le Smack est un Bisou &eacute;conomique, mais qui a peu d\'effets ]</span><br />',
            'A utiliser sans mod&eacute;ration !!<br />
				<span class="info">[ Le Baiser a un bon rapport qualit&eacute;/prix ]<br />
				[ Le Baiser peut prendre 10 fois plus de points d\'amour que le Smack ]</span><br />',
            'Pour ceux qui n\'ont pas peur d\'y aller à fond !!<br />
				<span class="info">[ Beaucoup plus efficace, mais très faible face aux Dents ]<br />
				[ Protège-le contre les Dents en montant le niveau de Langue ]<br />
				[ Le Baiser langoureux peut prendre 10 fois plus de points d\'amour que le Baiser ]</span><br />',
        ];
        if (isset($_POST['suppr_bisous']) && 0 == $joueurBloque) {
            $modif = false;
            for ($i = 0; $i != $nbType[1]; ++$i) {
                if (isset($_POST['sp'.$Obj[1][$i]]) && $nbE[1][$i] > 0) {
                    $nbSupp = $_POST['sp'.$Obj[1][$i]];
                    if ($nbSupp > 0 && $nbSupp <= $nbE[1][$i]) {
                        $nbE[1][$i] -= $nbSupp;
                        $modif = true;
                    }
                }
            }
            if ($modif) {
                $stmt = $pdo->prepare('UPDATE membres SET '.$Obj[1][0].' = :smack, '.$Obj[1][1].' = :baiser, '.$Obj[1][2].' = :pelle WHERE id = :id');
                $stmt->execute(['smack' => $nbE[1][0], 'baiser' => $nbE[1][1], 'pelle' => $nbE[1][2], 'id' => $id]);
            }
        }
    } elseif ('techno' === $page) {
        $evolPage = 2;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Apn&eacute;e',
            'Surprise',
            'Crachat',
            'Saut',
            'Manger de la soupe',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Cette technique permet d\'embrasser plus longtemps.<br />
				<span class="info">[ Augmente le nombre de Points d\'Amour vol&eacute;s ]</span><br />',
            'Permet d\'augmenter vos chances de r&eacute;ussite.<br />
				<span class="info">[ Augmente la force de tes Bisous lorsque tu tentes d\'embrasser quelqu\'un ]</span><br />',
            'C\'est une technique de d&eacute;fense très efficace.<br />
				<span class="info">[ Diminue le nombre de Points d\'Amour que l\'on peut te prendre ]</span><br />',
            'Saute de nuages en nuages !!<br />
				<span class="info">[ Permet de changer de Position et de Nuage ]<br />
				[ Augmente beaucoup la distance maximale de saut ]</span><br />',
            'Pour grandir, il faut manger de la soupe !!<br />
				<span class="info">[ Permet de diminuer le temps de cr&eacute;ation des organes ]<br />
				[ Permet de diminuer le temps de cr&eacute;ation des techniques ]</span><br />',
        ];
    }

    // Si on veut acceder a une des pages d'évolution -> prétraitement.
    if (-1 !== $evolPage) {
        include __DIR__.'/phpincludes/evo.php';
    }

    // Récupération du nombre de messages non lus.
    $stmt = $pdo->prepare('SELECT COUNT(*) AS nbMsg FROM messages WHERE destin = :destin AND statut = 0');
    $stmt->execute(['destin' => $id]);
    $nbNewMsg = $stmt->fetchColumn();
    if ($nbNewMsg > 0) {
        $NewMsgString = $nbNewMsg.' nouveau'.pluriel($nbNewMsg, 'x').' message'.pluriel($nbNewMsg);
    } else {
        $NewMsgString = 'Pas de nouveau message';
    }
}// Fin de partie pour gens connectés.

$temps12 = microtime_float();
// ***************************************************************************
/*Tache de vérification des évolutions.
    Ce code est effectué que l'on soit connecté ou non.
    Chaque fois que l'on demande une page au serveur, ce code est appellé.
    Il permet de créer une sorte de boucle de calcul virtuelle, pour peu qu'il y ait suffisament de gens qui se connectent.
*/
// On récupère les évolutions dont la date de création est atteinte ou dépassée.
$stmt = $pdo->prepare('SELECT auteur, id, type, classe, cout FROM evolution WHERE timestamp <= :timestamp');
$stmt->execute(['timestamp' => time()]);
// Boucle qui permet de traiter construction par construction.
while ($donnees_info = $stmt->fetch()) {
    // Id de l'auteur de la construction
    $id2 = $donnees_info['auteur'];
    // Id de la tache en question dans la base de donnée. (permet de la supprimer plus rapidement de la bdd)
    $idtache = $donnees_info['id'];
    // Classe de l'objet (exemple : batiment, bisou...)
    $classe = $donnees_info['classe'];
    // Type de l'objet
    $type = $donnees_info['type'];
    // Cout : pour déterminer le score.
    $coutObjet = $donnees_info['cout'];
    // On ajoute le nombre de points d'amour dépensés au score :
    AjouterScore($id2, $coutObjet);
    // On supprime la construction de la liste des taches.
    $stmt2 = $pdo->prepare('DELETE FROM evolution WHERE id = :id');
    $stmt2->execute(['id' => $idtache]);
    // On effectue la tache dans la table membre.
    $stmt2 = $pdo->prepare('SELECT '.$Obj[$classe][$type].', amour FROM membres WHERE id = :id');
    $stmt2->execute(['id' => $id2]);
    $donnees_info = $stmt2->fetch();
    $amourConstructeur = $donnees_info['amour'];
    // On récupère l'ancienne valeur.
    $nbObjEvol = $donnees_info[$Obj[$classe][$type]];
    // On augmente d'un.
    ++$nbObjEvol;
    // On met a jour la table.
    $stmt2 = $pdo->prepare('UPDATE membres SET '.$Obj[$classe][$type].' = :nb WHERE id = :id');
    $stmt2->execute(['nb' => $nbObjEvol, 'id' => $id2]);
    // Si le visiteur est connecté et membre, et si la construction est la sienne, on met a jour les infos sur la page.

    // S'il ya des constructions sur la liste de construction, on relance une construction.
    $stmt2 = $pdo->prepare('SELECT id, duree, type, cout FROM liste WHERE auteur = :auteur AND classe = :classe ORDER BY id LIMIT 0,1');
    $stmt2->execute(['auteur' => $id2, 'classe' => $classe]);
    if ($donnees_info = $stmt2->fetch()) {
        $timeFin2 = time() + $donnees_info['duree'];
        $stmt3 = $pdo->prepare('INSERT INTO evolution (timestamp, classe, type, auteur, cout) VALUES (:timestamp, :classe, :type, :auteur, :cout)');
        $stmt3->execute(['timestamp' => $timeFin2, 'classe' => $classe, 'type' => $donnees_info['type'], 'auteur' => $id2, 'cout' => $donnees_info['cout']]);
        $stmt3 = $pdo->prepare('DELETE FROM liste WHERE id = :id');
        $stmt3->execute(['id' => $donnees_info['id']]);
        if ($id == $id2) {
            $nbE[$classe][$type] = $nbObjEvol;
            if (1 == $classe) {
                // $amour -= $donnees_info['cout'];
            }
            // Pour l'affichage sur la page en cours.
            if ($evolPage == $classe) {
                $timeFin = $timeFin2;
                $evolution = $donnees_info['type'];
            }
        } elseif (1 == $classe) {
            // $amourConstructeur -= $donnees_info['cout'];
            // mysql_query("UPDATE membres SET amour=$amourConstructeur WHERE id=$id2");
        }
    } elseif ($id == $id2 && $evolPage == $classe) {
        $nbE[$classe][$type] = $nbObjEvol;
        // Permet a la page de savoir qu'il n'y a plus de construction en cours (pour l'affichage).
        $evolution = -1;
    }
}

// Gestion automatisée des attaques.
include __DIR__.'/phpincludes/attaque.php';

// ***************************************************************************
$temps13 = microtime_float();

// Gestion des différentes pages dispo.
include __DIR__.'/phpincludes/pages.php';

// Si on décide que la page existe.
if (isset($pages[$page])) {
    $title = $pages[$page]['title'].' - Bienvenue sur Bisouland';
    $include = 'phpincludes/'.$pages[$page]['file'];
} else {
    $title = 'Erreur 404 - Bienvenue sur Bisouland';
    $include = 'phpincludes/erreur404.php';
}
$temps31 = microtime_float();

if (false == $_SESSION['logged']) {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS nbre_entrees FROM connectbisous WHERE ip = :ip');
    $stmt->execute(['ip' => $_SERVER['REMOTE_ADDR']]);
    $donnees = $stmt->fetch();
    if (0 == $donnees['nbre_entrees']) { // L'ip ne se trouve pas dans la table, on va l'ajouter
        $stmt = $pdo->prepare('INSERT INTO connectbisous VALUES(:ip, :timestamp, 2)');
        $stmt->execute(['ip' => $_SERVER['REMOTE_ADDR'], 'timestamp' => time()]);
    } else { // L'ip se trouve déjà dans la table, on met juste à jour le timestamp
        $stmt = $pdo->prepare('UPDATE connectbisous SET timestamp = :timestamp WHERE ip = :ip');
        $stmt->execute(['timestamp' => time(), 'ip' => $_SERVER['REMOTE_ADDR']]);
    }
}
$temps32 = microtime_float();

// ETAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes
$timestamp_5min = time() - 300;
$stmt = $pdo->prepare('DELETE FROM connectbisous WHERE timestamp < :timestamp');
$stmt->execute(['timestamp' => $timestamp_5min]);

// Etape 3 : on demande maintenant le nombre de gens connectés.
// Nombre de visiteurs
$stmt = $pdo->query('SELECT COUNT(*) AS nbre_visit FROM connectbisous');
$donnees = $stmt->fetch();
$NbVisit = $donnees['nbre_visit'];
$stmt = $pdo->prepare('SELECT COUNT(*) AS nb_membres FROM membres WHERE lastconnect >= :lastconnect');
$stmt->execute(['lastconnect' => $timestamp_5min]);
$NbMemb = $stmt->fetchColumn();

$temps14 = microtime_float();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <title>
			<?php
                echo $title;
?>
		</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" media="screen" type="text/css" title="bisouStyle2" href="includes/bisouStyle2.css" />
		<link rel="shorcut icon" href="/favicon.ico"/>
		<meta name="description" content="<?php echo $title; ?>"/>
		<meta http-equiv="Content-Language" content="fr" />
    </head>

    <body>
<div id="superbig">

	<a href="/" id="Ban"></a>

	<ul id="speedbarre">

			<?php if (true == $_SESSION['logged']) {?>
				<li class="speedgauche">
					<strong><?php echo formaterNombre(floor($amour)); ?></strong> <img src="images/puce.png" title = "Nombre de points d'amour" alt="Nombre de points d'amour" />
				</li>
				<li class="speedgauche">Adoptez la strat&eacute;gie BisouLand !!</li>
				<li class="speeddroite">
					<a href="logout.html" title="Vous avez termin&eacute; ? D&eacute;connectez-vous !">D&eacute;connexion (<?php echo $_SESSION['pseudo']; ?>)</a>
				</li>
				<li class="speeddroite">
					<a href="boite.html" title="<?php echo $NewMsgString; ?>"><?php echo $NewMsgString; ?></a>
				</li>
			<?php
            } else {
                ?>

			<li class="speedgauche"><a href="connexion.html">Connexion</a></li>
			<li class="speeddroite">Adoptez la strat&eacute;gie BisouLand !! : <a href="inscription.html">Inscription</a></li>
			<?php } ?>

	</ul>
	<div id="pub">
	</div>

	<div id="Menu">
		<div class="sMenu">
            <h3>G&eacute;n&eacute;ral</h3>
			<ul>
				<li><a href="accueil.html">Accueil</a></li>
				<?php if (false == $_SESSION['logged']) {
                    ?>
				<li><a href="inscription.html">Inscription</a></li>
				<?php
                }
?>
				<li><a href="livreor.html">Livre d'or</a></li>
				<li><a href="stats.html">Statistiques</a></li>
				<li><a href="contact.html">Contact</a></li>
			</ul>
		</div>
    </div>

	<div id="dMenu">
		<div class="sMenu">
            <h3>BisouLand</h3>
			<ul>
				<?php if (true == $_SESSION['logged']) {?>
				<li><a href="cerveau.html">Cerveau</a></li>
				<li><a href="construction.html">Organes</a></li>
				<li><a href="techno.html">Techniques</a></li>
				<li><a href="bisous.html">Bisous</a></li>
				<li><a href="nuage.html">Nuages</a></li>
				<li><a href="boite.html">Messages</a></li>
				<li><a href="connected.html">Mon compte</a></li>
				<?php
                } else {
                    ?>
				<li>Tu n'es pas connect&eacute;.</li>
				<li><a href="connexion.html">Connexion</a></li>
				<?php
                }
?>
			</ul>
		</div>
		<div class="sMenu">
            <h3>Infos</h3>
			<ul>
				<li><a href="faq.html">FAQ</a></li>
				<li><a href="aide.html">Aide</a></li>
				<?php if (true == $_SESSION['logged']) {?>
				<li><a href="infos.html">Encyclop&eacute;die</a></li>
				<?php
                }
?>
				<li><a href="topten.html">Top 20</a></li>
				<li><a href="recherche.html">Recherche</a></li>
				<li><a href="membres.html">Joueurs</a></li>
			</ul>
		</div>
    </div>

	<div id="corps">
		<?php
            $temps15 = microtime_float();
include $include;
$temps16 = microtime_float();
?>
	</div>

<?php
    if (true == $_SESSION['logged']) {
        $stmt = $pdo->prepare('UPDATE membres SET lastconnect = :lastconnect, timestamp = :timestamp, amour = :amour WHERE id = :id');
        $stmt->execute(['lastconnect' => time(), 'timestamp' => time(), 'amour' => $amour, 'id' => $id]);
    }
?>

	<div id="Bas">
		<p>Il y a actuellement<strong>
<?php
echo $NbVisit.'</strong> visiteur';
if ($NbVisit > 1) {
    echo 's';
}
echo ' et <strong>'.$NbMemb.'</strong> membre';
if ($NbMemb > 1) {
    echo 's';
}
echo ' connect&eacute;';
if ($NbMemb + $NbVisit > 1) {
    echo 's';
}
?> sur BisouLand.</p>
<p><?php
$temps17 = microtime_float();
$temps_fin = microtime_float();
echo '<p class="Tpetit" >Page g&eacute;n&eacute;r&eacute;e en '.round($temps_fin - $temps_debut, 4).' secondes</p>';
/*
echo 'Bench temporaire, ne pas tenir compte :<br />';
echo 'T1 (compte):        '.round($temps12 - $temps11, 4).'<br />';
echo 'T2 (constructions): '.round($temps13 - $temps12, 4).'<br />';
echo 'T3 (pages+nb):      '.round($temps14 - $temps13, 4).'<br />';
echo 'T31 :               '.round($temps31 - $temps13, 4).'<br />';
echo 'T32 :               '.round($temps32 - $temps31, 4).'<br />';
echo 'T33 :               '.round($temps14 - $temps32, 4).'<br />';
echo 'T4 (tete):          '.round($temps15 - $temps14, 4).'<br />';
echo 'T5 (include):       '.round($temps16 - $temps15, 4).'<br />';
echo 'T6 (pied):          '.round($temps17 - $temps16, 4).'<br />';
*/
if (true == $_SESSION['logged'] && (12 == $id && 'cerveau' === $page)) {
    echo '<form class="Tpetit" method="post" action="accueil.html"><input type="submit" name="UnAct" tabindex="100" value="Action Unique" /></form>';
}
?>
</p>
		<p class="Tpetit">Tous droits r&eacute;serv&eacute;s &copy; BisouLand - Site respectant les r&egrave;gles de la CNIL</p>

    </div>


</div>
    </body>

</html>
