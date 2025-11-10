<?php

session_start();

include 'phpincludes/bd.php';

// Ensuite on vérifie que la variable $_SESSION['logged'] existe et vaut bien true.
if (isset($_SESSION['logged']) && true == $_SESSION['logged']) {
    $pdo = bd_connect();

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
} else {
    $_SESSION['errCon'] = 'Erreur : vous devez être connecté pour vous déconnecter !';
    $_SESSION['logged'] = false;
    header('location: connexion.html');
}
