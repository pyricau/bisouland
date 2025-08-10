<h1 id="retour" >FAQ</h1>
<br />
Cette section apporte des reponses a de nombreuses questions recurrentes.<br />
<br />
<?php


    $parties = [
        "Questions d'ordre general",
        "Questions sur le jeu",
    ];


$question[0] = [
    "Qu'est-ce que BisouLand ?",
    "Qui a cree BisouLand ?",
    "Combien cela coute t'il ?",
    "Comment s'inscrire a BisouLand ?",
    "Comment commencer a jouer ?",
    "Que faire si j'ai decouvert une erreur ?",
];

$rep[0] = [
    "BisouLand est un jeu de strategie multijoueurs. De nombreux joueurs se rencontrent en meme temps en ligne. Pour jouer, il suffit de disposer d'un simple navigateur.",
    "Le createur de BisouLand est Pierre-Yves Ricau, connu sous le pseudo Piwai alias admin sur BisouLand.",
    "BisouLand est totalement gratuit.",
    "Pour s'inscrire, il suffit d'aller sur la page Inscription et de remplir les champs appropries.<br />
		Ton compte sera automatiquement confirme a l'inscription.",
    "Il te suffit de te connecter, puis de lire les pages Aide et Encyclopedie,
		n'hesite pas a poser des questions en envoyant un message privé à l'admin.",
    "Merci de rapporter toute erreur, que ce soit un probleme technique ou une faute d'orthographe.<br />
		Vous pouvez pour cela creer un ticket sur https://github.com/pyricau/bisouland",
];

$question[1] = [
    "Comment fait t'on pour embrasser ?",
    "Qui puis-je embrasser ?",
    "Ou est le classement general ?",
    "Comment eviter d'etre embrasse ?",
    "Un adversaire n'arrete pas de m'embrasser.",
    "Puis-je donner mes Points d'Amour ?",
];

$rep[1] = [
    "Tout d'abord, il te faut plus de 50 points de score.<br />
		Ensuite, il faut que tu disposes de Bisous.<br />
		Une fois ces conditions reunies, va dans nuages, tu verras apparaitre en Rouge les joueurs que tu peux embrasser.<br />
		Clique sur le coeur dans la partie action pour embrasser un joueur.",

    "Tout joueur de plus de 50 points de score peut embrasser et etre embrasse.<br />
		Tu peux embrasser un joueur qui a plus ou moins 200 points de score par rapport a ton score.<br />
		A partir de 2000 points de score, les joueurs peuvent embrasser tout autre joueur au dessus de 2000.",

    "Il n'y a pas de classement general des joueurs, seulement un Top 20.<br />
		Cela permet d'eviter que les joueurs jugent du niveau d'un adversaire en regardant ses Points de Score.<br />
		Le Top 20 evoluera, de sorte a representer en moyenne environ 10% des joueurs.",

    "C'est impossible. De plus, il est impossible de se deplacer lorsque l'on est embrasse.<br />
		Neanmoins, si tu ne veux pas perdre tes Bisous, il te suffit d'embrasser un autre joueur.<br />
		Ainsi,tes Bisous ne seront pas pris en compte lorsque tu seras embrasse, et ils ne seront pas detruits.<br />
		Tu peux ensuite annuler l'action que tu as lance.",

    "Ne t'inquiete pas : il est impossible d'attaquer la meme personne plus de 3 fois toutes les 12 heures.<br />
		Ces 12 heures ne correspondent pas a des plages horaires. C'est 12 heures a compter de la premiere des 3 dernieres attaques.",

    "Il est impossible de donner des Points d'Amour a un autre joueur.",
];


//Les questions
foreach ($parties as $cleP => $Ftitre) {
    echo '<h1>' . $Ftitre . '</h1>
		<br />';
    foreach ($question[$cleP] as $cle => $qt) {
        echo '<a href="#r' . $cleP . $cle . '">' . $qt . '</a><br /><br />
			';
    }
}

//Les reponses
foreach ($parties as $cleP => $Ftitre) {
    echo '<h1>' . $Ftitre . '</h1>
		<br />';
    foreach ($rep[$cleP] as $cle => $rt) {
        echo '<h2 id="r' . $cleP . $cle . '">' . $question[$cleP][$cle] . '</h2>
			<br />
			' . $rt . '<br />
			<br />
			<a href="#retour">Retour en haut</a><br />
			';
    }
}
?>
