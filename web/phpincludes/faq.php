<h1 id="retour" >FAQ</h1>
<br />
Cette section apporte des réponses à de nombreuses questions récurrentes.<br />
<br />
<?php


	$parties = array(
		"Questions d'ordre général",
		"Questions sur le jeu"
	);

	
	$question[0] = array(
		"Qu'est-ce que BisouLand ?",
		"Qui a créé BisouLand ?",
		"Combien cela coûte t'il ?",
		"Comment s'inscrire à BisouLand ?",
		"Comment commencer à jouer ?",
		"Que faire si j'ai découvert une erreur ?"
	);

	$rep[0] = array(
		"BisouLand est un jeu de stratégie multijoueurs. De nombreux joueurs se rencontrent en même temps en ligne. Pour jouer, il suffit de disposer d'un simple navigateur.",
		"Le créateur de BisouLand est Pierre-Yves Ricau, connu sous le pseudo Piwaï alias admin sur BisouLand.",
		"BisouLand est totalement gratuit.",
		"Pour s'inscrire, il suffit d'aller sur la page Inscription et de remplir les champs appropriés.<br />
		N'oublie pas de donner une adresse email valide, car tu recevras une confirmation de ton compte par mail.",
		"Il te suffit de te connecter, puis de lire les pages Aide et Encyclopédie,
		n'hésite pas à poser des questions sur le tchat et le forum.",
		"Merci de rapporter toute erreur, que ce soit un problème technique ou une faute d'orthographe.<br />
		Vous pouvez pour cela creer un ticket sur https://github.com/pyricau/bisouland"
	);
		
	$question[1] = array(
		"Comment fait t'on pour embrasser ?",
		"Qui puis-je embrasser ?",
		"Où est le classement général ?",
		"Comment éviter d'être embrassé ?",
		"Un adversaire n'arrete pas de m'embrasser.",
		"Puis-je donner mes Points d'Amour ?"
	);

	$rep[1] = array(
		"Tout d'abord, il te faut plus de 50 points de score.<br />
		Ensuite, il faut que tu disposes de Bisous.<br />
		Une fois ces conditions réunies, va dans nuages, tu verras apparaitre en Rouge les joueurs que tu peux embrasser.<br />
		Clique sur le coeur dans la partie action pour embrasser un joueur.",
		
		"Tout joueur de plus de 50 points de score peut embrasser et être embrassé.<br />
		Tu peux embrasser un joueur qui a plus ou moins 200 points de score par rapport à ton score.<br />
		A partir de 2000 points de score, les joueurs peuvent embrasser tout autre joueur au dessus de 2000.",
		
		"Il n'y a pas de classement général des joueurs, seulement un Top 20.<br />
		Cela permet d'éviter que les joueurs jugent du niveau d'un adversaire en regardant ses Points de Score.<br />
		Le Top 20 évoluera, de sorte à représenter en moyenne environ 10% des joueurs.",
		
		"C'est impossible. De plus, il est impossible de se déplacer lorsque l'on est embrassé.<br />
		Néanmoins, si tu ne veux pas perdre tes Bisous, il te suffit d'embrasser un autre joueur.<br />
		Ainsi,tes Bisous ne seront pas pris en compte lorsque tu seras embrassé, et ils ne seront pas détruits.<br />
		Tu peux ensuite annuler l'action que tu as lancé.",
		
		"Ne t'inquiete pas : il est impossible d'attaquer la même personne plus de 3 fois toutes les 12 heures.<br />
		Ces 12 heures ne correspondent pas à des plages horaires. C'est 12 heures à compter de la première des 3 dernières attaques.",
		
		"Il est impossible de donner des Points d'Amour à un autre joueur."
	);
	
	
	//Les questions
	foreach ($parties as $cleP=>$Ftitre)
	{
		echo '<h1>'.$Ftitre.'</h1>
		<br />';
		foreach ($question[$cleP] as $cle=>$qt)
		{
			echo '<a href="#r'.$cleP.$cle.'">'.$qt.'</a><br /><br />
			';
		}
	}
	
	//Les réponses
	foreach ($parties as $cleP=>$Ftitre)
	{
		echo '<h1>'.$Ftitre.'</h1>
		<br />';
		foreach ($rep[$cleP] as $cle=>$rt)
		{
			echo '<h2 id="r'.$cleP.$cle.'">'.$question[$cleP][$cle].'</h2>
			<br />
			'.$rt.'<br />
			<br />
			<a href="#retour">Retour en haut</a><br />
			';
		}
	}
?>
