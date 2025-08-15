<?php

/**
 * # Other files
 * ## config
 * parameters.php // Config
 * 
 * ## news
 * chemin.php // @todo check
 * liste_news.php // @todo check
 * rediger_news.php // @todo check
 *
 * ## phpincludes
 * attaque.php // game logic @todo check
 * bd.php // logic for database connection
 * connexion.php // logic for login
 * erreur404.php // not found error page
 * evo.php // game logic @todo check
 * fctIndex.php // functions @todo check
 * pages.php // Router (this file)
 *
 * ## web
 * deconnexion.php // logic for logout @todo check
 * index.php // Front Controller / layout
 * redirect.php // logic for connexion.php form submission
 * reductionNuages.php // script for Clouds Removal @todo use on account removal
 */

// Gestion des pages
$array_pages = [
    'accueil' => 'accueil.php', // Homepage
    'inscription' => 'inscription.php', // SignUp
    'confirmation' => 'confirmation.php', // @todo use
    'connected' => 'connected.php', // LoggedIn/Account
    'membres' => 'membres.php', // Players
    'construction' => 'construction.php', // LoggedIn/Organs
    'bisous' => 'bisous.php', // LoggedIn/Kiss
    'livreor' => 'livreor.php', // Guestbook
    'aide' => 'aide.php', // Help
    'lire' => 'lire.php', // LoggedIn/ViewMessage
    'boite' => 'boite.php', // LoggedIn/Inbox
    'techno' => 'techno.php', // LoggedIn/Techniques
    'nuage' => 'nuage.php', // LoggedIn/Clouds
    'infos' => 'infos.php', // LoggedIn/Reference
    'contact' => 'contact.php', // Contact
    'action' => 'action.php', // LoggedIn/BlowKisses
    'changepass' => 'changepass.php', // LoggedIn/ChangePassword
    'cerveau' => 'cerveau.php', // LoggedIn/Brain
    'topten' => 'topten.php', // Ranking
    'stats' => 'stats.php', // Statistics
    'recherche' => 'recherche.php', // Search
    'connexion' => 'connexion.php', // LogIn
    'yeux' => 'yeux.php', // LoggedIn/Eyes
    'faq' => 'faq.php', // Faq
];

$array_titres = [
    'accueil' => 'Accueil',
    'inscription' => 'Inscription',
    'confirmation' => 'Confirmation',
    'connected' => 'Mon compte',
    'membres' => 'Membres',
    'construction' => 'Organes',
    'bisous' => 'Cr&eacute;er des bisous',
    'livreor' => "Livre d'or",
    'aide' => 'Aide',
    'lire' => 'Lire un message',
    'boite' => 'Messages priv&eacute;s',
    'techno' => 'Techniques diverses et vari&eacute;es',
    'nuage' => 'La tête dans les nuages...',
    'infos' => 'Encyclop&eacute;die',
    'contact' => 'Contact',
    'action' => 'Action',
    'changepass' => 'Changer votre mot de passe',
    'cerveau' => 'Cerveau',
    'topten' => 'Meilleurs Joueurs',
    'stats' => 'Statistiques',
    'recherche' => 'Recherche',
    'connexion' => 'Connexion',
    'yeux' => 'D&eacute;visager un joueur',
    'faq' => 'FAQ',
];
