<?php

header('Content-type: text/html; charset=ISO-8859-1');

    //Démarrage de la session
    session_start();
    ob_start();

    include 'phpincludes/bd.php';
    bd_connect();

    include('phpincludes/fctIndex.php');

    $inMainPage=true;

    //Si la variable $_SESSION['logged'] n'existe pas, on la créée, et on l'initialise a false
    if (!isset($_SESSION['logged'])) $_SESSION['logged'] = false;

    //Gestion de la page courante : Permet de désigner la page a inclure. Si la variable est vide, alors ca sera 'accueil'.
    $page = (!empty($_GET['page'])) ? htmlentities($_GET['page'], ENT_IGNORE) : 'accueil';

    //Test en cas de suppression de compte
    //Il faudra a jouter ici une routine de suppression des messages dans la bdd.
    //Ainsi que des constructions en cours, etc..
    if (isset($_POST['suppr']))
    {
        if ($_SESSION['logged'] == true)
        {
            $_SESSION['pseudo'] = "Not Connected";
            $_SESSION['logged'] = false;
            SupprimerCompte($_SESSION['id']);

        }
    }
    $pseudo = $_SESSION['pseudo'];

    //Informations nécessaires au fonctionnement du jeu.

    $nbType = array(
        6,
        3,
        5
    );

    $Obj[0] = array(
        'coeur',
        'bouche',
        'langue',
        'dent',
        'jambes',
        'oeil'
    );

    $Obj[1] = array(
        'smack',
        'baiser',
        'pelle',
    );

    $Obj[2] = array(
        'tech1',
        'tech2',
        'tech3',
        'tech4',
        'soupe'
    );


//***************************************************************************
    //Si on est connecté
    if ($_SESSION['logged'] == true)
    {

        //l'id du membre.
        $id=$_SESSION['id'];

        $sql_info = mysql_query("SELECT timestamp, coeur, bouche, amour, jambes, smack, baiser, pelle, tech1, tech2, tech3, tech4, dent, langue, bloque, soupe, oeil FROM membres WHERE id='".$id."'");
        $donnees_info = mysql_fetch_assoc($sql_info);
        //Date du dernier calcul du nombre de points d'amour.
        $lastTime = $donnees_info['timestamp'];
        //Temps écoulé depuis le dernier calcul.
        $timeDiff = time() - $lastTime;

        //On récupère le nombre de points d'amour.
        $amour = $donnees_info['amour'];

        $joueurBloque=$donnees_info['bloque'];

        //Nombre d'objets d'un type donné.
        //Batiments

        $nbE = array();

        for ($i = 0; $i < 3; $i++)
        {
            for ($j = 0; $j < $nbType[$i]; $j++)
            {
                $nbE[$i][$j] = $donnees_info[$Obj[$i][$j]];
            }
        }

        //Cout en point d'amour pour la construction d'un objet
        //Organes
        $amourE[0] = array(
            expo(100, 0.4, $nbE[0][0], 1),
            expo(200, 0.4, $nbE[0][1], 1),
            expo(250, 0.4, $nbE[0][2], 1),
            expo(500, 0.4, $nbE[0][3], 1),
            expo(1000, 0.6, $nbE[0][4], 1),
            expo(1000, 0.4, $nbE[0][5], 1)
        );

        //Bisous
        $amourE[1] = array(
            800,
            3500,
            10000
        );

        //Technos
        $amourE[2] = array(
            expo(1000, 0.4, $nbE[2][0], 1),
            expo(2000, 0.4, $nbE[2][1], 1),
            expo(3000, 0.4, $nbE[2][2], 1),
            expo(10000, 0.6, $nbE[2][3], 1),
            expo(5000, 0.4, $nbE[2][4], 1)
        );

        //Temps pour la construction de l'objet.
        //Organes
        $tempsE[0] = array(
            ExpoSeuil(235000,20,$nbE[0][0]-$nbE[2][4],1),
            ExpoSeuil(200000,25,$nbE[0][1]-$nbE[2][4],1),
            ExpoSeuil(220000,22,$nbE[0][2]-$nbE[2][4],1),
            ExpoSeuil(210000,17,$nbE[0][3]-$nbE[2][4],1),
            ExpoSeuil(1000000,5,$nbE[0][4]-$nbE[2][4],1),
            ExpoSeuil(500000,5,$nbE[0][5]-$nbE[2][4],1)
        );

        //Bisous
        $tempsE[1] = array(
            InvExpo(100, 1.5, $nbE[0][1], 1),
            InvExpo(250, 1.7, $nbE[0][1], 1),
            InvExpo(500, 2, $nbE[0][1], 1)
        );

        //Tech
        $tempsE[2] = array(
            expo(50, 0.4, $nbE[2][0]-$nbE[2][4], 1),
            expo(1000, 0.4, $nbE[2][1]-$nbE[2][4], 1),
            expo(3000, 0.4, $nbE[2][2]-$nbE[2][4], 1),
            expo(15000, 0.6, $nbE[2][3]-$nbE[2][4], 1),
            expo(5000, 0.3, $nbE[2][4], 1)
        );

        $amour=calculterAmour($amour,$timeDiff,$nbE[0][0],$nbE[1][0],$nbE[1][1],$nbE[1][2]);
        //Mise a jour du nombre de points d'amour, par rapport au temps écoulé.

        //Gestion des pages d'évolution (constructions).
        $evolPage=-1; //Valeur par défaut.
        if ($page=='construction')
        {
            $evolPage=0;
            //Nom de chaque objet d'un type différent.
            $evolNom = array(
                'Coeur',
                'Bouche',
                'Langue',
                'Dents',
                'Jambes',
                'Yeux'
            );
            //Description qui accompagne l'objet.
            $evolDesc = array(
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
                [ Chaque niveau diminue les chances d\'obtenir plus d\'information sur vous ]</span><br />'
            );
        }
        elseif ($page=='bisous')
        {
            $evolPage=1;
            //Nom de chaque objet d'un type différent.
            $evolNom = array(
                'Smacks',
                'Baisers',
                'Baisers langoureux'
            );
            //Description qui accompagne l'objet.
            $evolDesc = array(
                'Pour tenter une première approche.<br />
                <span class="info">[ Le Smack est un Bisou &eacute;conomique, mais qui a peu d\'effets ]</span><br />',
                'A utiliser sans mod&eacute;ration !!<br />
                <span class="info">[ Le Baiser a un bon rapport qualit&eacute;/prix ]<br />
                [ Le Baiser peut prendre 10 fois plus de points d\'amour que le Smack ]</span><br />',
                'Pour ceux qui n\'ont pas peur d\'y aller à fond !!<br />
                <span class="info">[ Beaucoup plus efficace, mais très faible face aux Dents ]<br />
                [ Protège-le contre les Dents en montant le niveau de Langue ]<br />
                [ Le Baiser langoureux peut prendre 10 fois plus de points d\'amour que le Baiser ]</span><br />'
            );
            if (isset($_POST['suppr_bisous']) && $joueurBloque==0)
            {
                $modif=false;
                for($i=0;$i!=$nbType[1];$i++)
                {
                    if (isset($_POST['sp'.$Obj[1][$i]]) && $nbE[1][$i]>0)
                    {

                        $nbSupp=$_POST['sp'.$Obj[1][$i]];
                        if ($nbSupp>0 && $nbSupp<=$nbE[1][$i])
                        {
                            $nbE[1][$i]-=$nbSupp;
                            $modif=true;
                        }
                    }
                }
                if ($modif==true)
                {
                    mysql_query("UPDATE membres SET ".$Obj[1][0]."='".$nbE[1][0]."', ".$Obj[1][1]."='".$nbE[1][1]."', ".$Obj[1][2]."='".$nbE[1][2]."' WHERE id='".$id."'");
                }
            }
        }
        elseif ($page=='techno')
        {
            $evolPage=2;
            //Nom de chaque objet d'un type différent.
            $evolNom = array(
                'Apn&eacute;e',
                'Surprise',
                'Crachat',
                'Saut',
                'Manger de la soupe'
            );
            //Description qui accompagne l'objet.
            $evolDesc = array(
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
                [ Permet de diminuer le temps de cr&eacute;ation des techniques ]</span><br />'
            );
        }

        //Si on veut acceder a une des pages d'évolution -> prétraitement.
        if ($evolPage != -1)
        {
            include('phpincludes/evo.php');
        }

        //Récupération du nombre de messages non lus.
        $retour = mysql_query("SELECT COUNT(*) AS nbMsg FROM messages WHERE destin=".$id." AND statut = 0");
        $nbNewMsg = mysql_result($retour,0,'nbMsg');
        if ($nbNewMsg>0)
        {
            $NewMsgString = $nbNewMsg.' nouveau'.pluriel($nbNewMsg,'x').' message'.pluriel($nbNewMsg);
        }
        else
        {
            $NewMsgString = 'Pas de nouveau message';
        }

        //Permet d'envoyer des messages au Mini Tchat :
        if (isset($_POST['chat']) && !empty($_POST['chat']))
        {

            $chatmess=$_POST['chat'];

            if(strlen($chatmess )> 100)
            {
                $chatmess = substr($chatmess,0,94).' [...]';
            }

            //Pas de addslashes, mais protégé.


            if (!preg_match("![^ ]{14,}!", $chatmess))
            {
                $chatmess = addslashes(htmlentities ($chatmess, ENT_IGNORE));
                $requete = mysql_query("SELECT message FROM chatbisous ORDER BY id DESC LIMIT 1");
                $last = mysql_fetch_assoc($requete);
                if ($last['message']!=$chatmess)
                {
                    mysql_query("INSERT INTO chatbisous VALUES('', '$pseudo', '$chatmess', ".time().")");
                    mysql_query("DELETE FROM chatbisous ORDER BY id LIMIT 1");
                }
            }
        }

    }//Fin de partie pour gens connectés.

    //***************************************************************************
    /*Tache de vérification des évolutions.
        Ce code est effectué que l'on soit connecté ou non.
        Chaque fois que l'on demande une page au serveur, ce code est appellé.
        Il permet de créer une sorte de boucle de calcul virtuelle, pour peu qu'il y ait suffisament de gens qui se connectent.
    */
    //On récupère les évolutions dont la date de création est atteinte ou dépassée.
    $sql_info = mysql_query("SELECT auteur, id, type, classe, cout FROM evolution WHERE timestamp<='".time()."'");
    //Boucle qui permet de traiter construction par construction.
    while ($donnees_info = mysql_fetch_assoc($sql_info))
    {
        //Id de l'auteur de la construction
        $id2 = $donnees_info['auteur'];
        //Id de la tache en question dans la base de donnée. (permet de la supprimer plus rapidement de la bdd)
        $idtache = $donnees_info['id'];
        //Classe de l'objet (exemple : batiment, bisou...)
        $classe = $donnees_info['classe'];
        //Type de l'objet
        $type = $donnees_info['type'];
        //Cout : pour déterminer le score.
        $coutObjet = $donnees_info['cout'];
        //On ajoute le nombre de points d'amour dépensés au score :
        AjouterScore($id2,$coutObjet);
        //On supprime la construction de la liste des taches.
        mysql_query("DELETE FROM evolution WHERE id='".$idtache."'");
        //On effectue la tache dans la table membre.
        $sql_info2 = mysql_query("SELECT ".$Obj[$classe][$type].", amour FROM membres WHERE id='".$id2."'");
        $donnees_info = mysql_fetch_assoc($sql_info2);
        $amourConstructeur=$donnees_info['amour'];
        //On récupère l'ancienne valeur.
        $nbObjEvol = $donnees_info[$Obj[$classe][$type]];
        //On augmente d'un.
        $nbObjEvol++;
        //On met a jour la table.
        mysql_query("UPDATE membres SET ".$Obj[$classe][$type]."='".$nbObjEvol."' WHERE id='".$id2."'");
        //Si le visiteur est connecté et membre, et si la construction est la sienne, on met a jour les infos sur la page.

        //S'il ya des constructions sur la liste de construction, on relance une construction.
        $sql_info2 = mysql_query("SELECT id, duree, type, cout FROM liste WHERE auteur=$id2 AND classe=$classe ORDER BY id LIMIT 0,1");
        if($donnees_info = mysql_fetch_assoc($sql_info2))
        {
            $timeFin2 = time() + $donnees_info['duree'];
            mysql_query("INSERT INTO evolution (id, timestamp, classe, type, auteur, cout) VALUES ('', '".$timeFin2."', $classe, ".$donnees_info['type'].", $id2, ".$donnees_info['cout'].")");
            mysql_query("DELETE FROM liste WHERE id=".$donnees_info['id']);
            if ($id==$id2)
            {
                $nbE[$classe][$type]=$nbObjEvol;
                if ($classe==1)
                {
                    //$amour -= $donnees_info['cout'];
                }
                //Pour l'affichage sur la page en cours.
                if ($evolPage == $classe)
                {
                    $timeFin = $timeFin2;
                    $evolution = $donnees_info['type'];
                }
            }
            else
            {
                if ($classe==1)
                {
                    //$amourConstructeur -= $donnees_info['cout'];
                    //mysql_query("UPDATE membres SET amour=$amourConstructeur WHERE id=$id2");
                }
            }
        }
        else
        {


        if ($id==$id2)
        {
            if ($evolPage == $classe)
            {
                $nbE[$classe][$type]=$nbObjEvol;
                //Permet a la page de savoir qu'il n'y a plus de construction en cours (pour l'affichage).
                $evolution = -1;
            }
        }
        }
    }

    //Gestion automatisée des attaques.
    include('phpincludes/attaque.php');

    //***************************************************************************

    //Gestion des différentes pages dispo.
    include('phpincludes/pages.php');

    //Si on décide que la page existe.
    if (isset($array_pages[$page]))
    {
        $title=$array_titres[$page].' - Bienvenue sur Bisouland';
        $include='phpincludes/'.$array_pages[$page];
    }
    else
    {
        $title = 'Erreur 404 - Bienvenue sur Bisouland';
        $include='phpincludes/erreur404.php';
    }

    //Etape 3 : on demande maintenant le nombre de gens connectés.
    $retour = mysql_query("SELECT COUNT(*) AS nb_membres FROM membres WHERE lastconnect>=".$timestamp_5min);
    $NbMemb=mysql_result($retour,0,'nb_membres');
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
    <html class="no-js lt-ie9 lt-ie8 lt-ie7">
<![endif]-->
<!--[if IE 7]>
    <html class="no-js lt-ie9 lt-ie8">
<![endif]-->
<!--[if IE 8]>
    <html class="no-js lt-ie9">
<![endif]-->
<!--[if gt IE 8]>
    <!--><html class="no-js" lang="fr">
<!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php echo $title;?>
        </title>
        <meta name="description" content="<?php echo $title;?>">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" type="text/css" media="screen" href="css/bisouland.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="includes/bisouStyle2.css" />
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong>
            browser. Please <a href="http://browsehappy.com/">upgrade your browser</a>
            or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a>
            to improve your experience.</p>
        <![endif]-->

        <header class="main-menu">
            <nav class="content">
                <h1 class="brand">
                    <a href="/">
                        Bisouland v3
                    </a>
                </h1>

                <ul class="menu">
                    <?php if ($_SESSION['logged'] == false): ?>
                        <li class="first">
                            <a href="/login">
                                Connexion
                            </a>
                        </li>
                        <li class="last">
                            <a href="/register/">
                                Pré-Inscription
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="first">
                            <strong><?php echo formaterNombre(floor($amour)); ?></strong>
                            <img src="/images/puce.png" title = "Nombre de points d'amour" alt="Nombre de points d'amour" />
                        </li>
                        <li>
                            <a href="/account">
                                Mon compte
                            </a>
                        </li>
                        <li class="last">
                            <a href="boite.html" title="<?php echo $NewMsgString; ?>">
                                <?php echo $NewMsgString ;?>
                            </a>
                        </li>
                        <li>
                            <a href="/logout">
                                Déconnexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <div class="page">
            <div id="Menu">
                <div class="sMenu">
                    <h3>G&eacute;n&eacute;ral</h3>
                    <ul>
                        <li><a href="livreor.html">Livre d'or</a></li>
                        <li><a href="stats.html">Statistiques</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="sMenu">
                    <h3>Mini Chat</h3>
                    <?php
                    if ($_SESSION['logged'] == true)
                    {
                    ?>
                    <form action="<?php echo $page;?>.html" method="post">
                        <p>
                            <label>Message :<input type="text" name="chat" size="12" maxlength="90"/></label><br /><br />
                            <input class="Rbutton" type="submit" value="Envoyer" />
                        </p>
                    </form>
                    <?php
                    }
                    ?>
                    <a style="font-size:0.8em;" href="tchat.html">Plus de messages</a>
                    <?php
                        $reponse = mysql_query("SELECT pseudo, message, timestamp FROM chatbisous ORDER BY id DESC LIMIT 0,5");

                        while ($donnees = mysql_fetch_assoc($reponse) )
                        {
                            $message=smileys($donnees['message']);
                            echo '<p><strong>',$donnees['pseudo'],'</strong>',date(' [H\hi] : ', $donnees['timestamp']),$message,'</p>';
                        }
                    ?>
                </div>
            </div>

            <div id="dMenu">
                <div class="sMenu">
                    <h3>BisouLand</h3>
                    <ul>
                        <?php if ($_SESSION['logged'] == true)
                        {?>
                        <li><a href="cerveau.html">Cerveau</a></li>
                        <li><a href="construction.html">Organes</a></li>
                        <li><a href="techno.html">Techniques</a></li>
                        <li><a href="bisous.html">Bisous</a></li>
                        <li><a href="nuage.html">Nuages</a></li>
                        <li><a href="boite.html">Messages</a></li>
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
                        <?php if ($_SESSION['logged'] == true)
                        {?>
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

            <div class="main-content">
                <?php include($include); ?>
            </div>

			<?php if ($_SESSION['logged'] == true): ?>
	        	<?php mysql_query("UPDATE membres SET lastconnect=".time().", timestamp='".time()."' , amour='".$amour."' WHERE id='".$id."'"); ?>
	        <?php endif; ?>
        </div>

        <script src="js/bisouland.min.js"></script>

        <script>
            var _gaq=[['_setAccount','UA-32299315-1'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>
