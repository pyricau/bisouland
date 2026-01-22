<?php

/**
 * # Other files
 * ## config
 * parameters.php // Config.
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
 */

// Gestion des pages
$pages = [
    // Public Pages
    'accueil' => [ // Homepage
        'file' => 'accueil.php',
        'title' => 'Accueil',
    ],
    'aide' => [ // Help
        'file' => 'aide.php',
        'title' => 'Aide',
    ],
    'connexion' => [ // LogIn
        'file' => 'connexion.php',
        'title' => 'Connexion',
    ],
    'contact' => [ // Contact
        'file' => 'contact.php',
        'title' => 'Contact',
    ],
    'faq' => [ // Faq
        'file' => 'faq.php',
        'title' => 'FAQ',
    ],
    'inscription' => [ // SignUp
        'file' => 'inscription.php',
        'title' => 'Inscription',
    ],
    'livreor' => [ // Guestbook
        'file' => 'livreor.php',
        'title' => "Livre d'or",
    ],
    'membres' => [ // Players
        'file' => 'membres.php',
        'title' => 'Membres',
    ],
    'recherche' => [ // Search
        'file' => 'recherche.php',
        'title' => 'Recherche',
    ],
    'stats' => [ // Statistics
        'file' => 'stats.php',
        'title' => 'Statistiques',
    ],
    'topten' => [ // Ranking
        'file' => 'topten.php',
        'title' => 'Meilleurs Joueurs',
    ],

    // Logged in Player Pages
    'action' => [ // BlowKisses
        'file' => 'action.php',
        'title' => 'Action',
    ],
    'bisous' => [ // Kiss
        'file' => 'bisous.php',
        'title' => 'Cr&eacute;er des bisous',
    ],
    'cerveau' => [ // Brain
        'file' => 'cerveau.php',
        'title' => 'Cerveau',
    ],
    'changepass' => [ // ChangePassword
        'file' => 'changepass.php',
        'title' => 'Changer votre mot de passe',
    ],
    'connected' => [ // Account
        'file' => 'connected.php',
        'title' => 'Mon compte',
    ],
    'nuage' => [ // Clouds
        'file' => 'nuage.php',
        'title' => 'La tête dans les nuages...',
    ],
    'construction' => [ // Organs
        'file' => 'construction.php',
        'title' => 'Organes',
    ],
    'boite' => [ // Inbox
        'file' => 'boite.php',
        'title' => 'Notifications',
    ],
    'infos' => [ // Reference
        'file' => 'infos.php',
        'title' => 'Encyclop&eacute;die',
    ],
    'techno' => [ // Techniques
        'file' => 'techno.php',
        'title' => 'Techniques diverses et vari&eacute;es',
    ],
    'lire' => [ // ViewMessage
        'file' => 'lire.php',
        'title' => 'Lire une notification',
    ],
    'yeux' => [ // Eyes
        'file' => 'yeux.php',
        'title' => 'D&eacute;visager un joueur',
    ],
];
