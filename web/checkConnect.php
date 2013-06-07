<?php

	include 'phpincludes/fctIndex.php';
	include 'phpincludes/bd.php';

        bd_connect();

	$message_texte="Bonjour !!
	Tu reçois ce message car tu es inscrit sur le site de jeu multijoueur BisouLand.
	Tu ne t'es pas connecté sur BisouLand depuis plus de 31 jours.
	Si tu ne te connectes pas avant 7 jours, ton compte sera supprimé sans préavis.
	Nous te rappellons que l'adresse de BisouLand est :
	
	http://bisouland.piwai.info
	
	Plein de Bisous,
	L'équipe BisouLand
	";

	$message_html=nl2br(htmlentities($message_texte), ENT_IGNORE);
	
	$objet="N'oublie pas de te connecter à BisouLand";
	
	$tempsUneSemaine=time()-604800;
	$tempsUnMois=time()-2678400;
	
	//Si les joueurs se sont connectés depuis un mois, on enlève l'avertissement.
	$sql= mysql_query("SELECT id FROM membres WHERE averto>0 AND lastconnect>$tempsUnMois");
	while($donnees = mysql_fetch_assoc($sql))
	{
		mysql_query("UPDATE membres SET averto=0 WHERE id=".$donnees['id']);
	}	

	$sql= mysql_query("SELECT id,averto,email,confirmation FROM membres WHERE lastconnect<$tempsUnMois");

	//Boucle de traitement des informations.
	while($donnees = mysql_fetch_assoc($sql))
	{

		if ($donnees['id']!=1)
		{
		//Si compte non confirmé, on le supprime. (et que c'est po BisouLand)
		if($donnees['confirmation']==0)
		{
			SupprimerCompte($donnees['id']);
		}
		else
		{
			//Si pas encore eu d'avertissement :
			if ($donnees['averto']==0)
			{
				//On envoie un avertissement.
				envoyerMail($donnees['email'],$message_html,$message_texte,$objet);

				mysql_query("UPDATE membres SET averto=".time()." WHERE id=".$donnees['id']);
			}
			else if($donnees['averto']<$tempsUneSemaine)
			{
				//On va pas embeter l'utilisateur avec un nouveau mail, s'il n'a pas répondu a l'avertissement, on supprime son compte.
				SupprimerCompte($donnees['id']);
				$i++;
			}
		}
		}
	}

?>
