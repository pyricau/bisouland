<?php

session_start();

include 'phpincludes/bd.php';

//Ensuite on vérifie que la variable $_SESSION['logged'] existe et vaut bien true.
if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {

    bd_connect();

    $timeDeco = time() - 600;
    mysql_query("UPDATE membres SET lastconnect=".$timeDeco." WHERE id=".$_SESSION['id']);
    //On modifie la valeur de $_SESSION['logged'], qui devient false.
    $_SESSION['logged'] = false;
    $timestamp_expire = time() - 1000;
    setcookie('pseudo', '', $timestamp_expire);
    setcookie('mdp', '', $timestamp_expire);

    //Redirection.
    header("location: accueil.html");
} else {
    $_SESSION['errCon'] = 'Erreur : vous devez être connecté pour vous déconnecter !';
    $_SESSION['logged'] = false;
    header("location: connexion.html");
}
