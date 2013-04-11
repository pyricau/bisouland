<?php
if (isset($inMainPage) && $inMainPage==true)
{	
	//***************************************************************************
	//Gestion des attaques.
	//Phase d'aller :
	$sql_info = mysql_query("SELECT finaller, auteur, cible FROM attaque WHERE finaller<='".time()."' AND finaller!=0");
	while ($donnees_info = mysql_fetch_assoc($sql_info))
	{
		$idAuteur=$donnees_info['auteur'];
		$idCible=$donnees_info['cible'];
		$finaller=$donnees_info['finaller'];
		mysql_query("UPDATE attaque SET finaller='0' WHERE auteur='".$idAuteur."'");
		
		//On indique que l'attaque a eu lieu.
		mysql_query("INSERT INTO logatt VALUES($idAuteur, $idCible, $finaller)");
		//Supprimer ceux vieux de plus de 12 heures.
		$timeAtt=time()-43200;
		mysql_query("DELETE FROM logatt WHERE timestamp<$timeAtt");
		
		/*
		Quelques notes :
		Avantages attaquant : 
			Bouche : Plus de forces pour les baisers (coefficient global mais faible)
			Apnée : prend plus de points d'amour (pourcentage)
			Surprise : attaque plus forte (coefficient global)
			Langue : baisers langoureux sont plus forts
		Avantages défenseur :
			Bouche : Plus de forces pour les baisers (coefficient global mais faible)
			Crachat : L'attaquant prend moins de points d'amour (pourcentage)
			Dents : Défense plus forte (coeff global) ET plus de chances de détruire les baisers langoureux ennemis.
			Langue : baisers langoureux sont plus forts
		*/
		//Infos attaquant :
		$sql_info3 = mysql_query("SELECT bouche, smack, baiser, pelle, tech1, tech2, langue, score FROM membres WHERE id=".$idAuteur);
		$donnees_info3 = mysql_fetch_assoc($sql_info3);
		$AttSmack=$donnees_info3['smack'];
		$AttBaiser=$donnees_info3['baiser'];
		$AttPelle=$donnees_info3['pelle'];
		$AttApnee=$donnees_info3['tech1'];
		$AttSurprise=$donnees_info3['tech2'];
		$AttBouche=$donnees_info3['bouche'];
		$AttLangue=$donnees_info3['langue'];
		$AttScore=$donnees_info3['score'];
		
		$sql_info4 = mysql_query("SELECT coeur, timestamp, bouche, amour, smack, baiser, pelle, tech3, dent, langue, bloque, score FROM membres WHERE id='".$idCible."'");
		$donnees_info4 = mysql_fetch_assoc($sql_info4);
		$DefSmack=$donnees_info4['smack'];
		$DefBaiser=$donnees_info4['baiser'];
		$DefPelle=$donnees_info4['pelle'];
		$DefCrachat=$donnees_info4['tech3'];
		$DefBouche=$donnees_info4['bouche'];
		$DefLangue=$donnees_info4['langue'];
		$DefDent=$donnees_info4['dent'];
		$DefBloque=$donnees_info4['bloque'];
		$DefScore=$donnees_info4['score'];
		
		//Gestion de l'attaque (coeff * bisous):
		$AttForce = (1 + (0.1*$AttBouche) + (0.5*$AttSurprise))*($AttSmack + (2.1*$AttBaiser) + ((3.5+0.2*$AttLangue)*$AttPelle));
		
		$DefForce = (1 + (0.1*$DefBouche) + (0.7*$DefDent))*($DefSmack + (2.1*$DefBaiser) + ((3.5+0.2*$DefLangue)*$DefPelle));
		//Si on est déjà en attaque, on diminue considérablement la force de défense.
		if ($DefBloque==1)
		{
			$somme=($DefSmack + $DefBaiser + $DefPelle);
			if ($somme==0){$somme=1;}
			$DefForce/=$somme;
		}
		
		$bilan=$AttForce-$DefForce;
		if ($bilan<0)
		{
			$AttSmack=0;
			$AttBaiser=0;
			$AttPelle=0;
			$coeffBilan=$AttForce/$DefForce;
			//Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
			if ($DefBloque==0)
			{
				$DefSmack=floor($DefSmack*(1 - $coeffBilan/rand(2,10)));
				$DefBaiser=floor($DefBaiser*(1 - $coeffBilan/rand(2,10)));
				$DefPelle=floor($DefPelle*(1 - $coeffBilan/rand(2,10)));
			}
			
			//Attaque terminée, plus rien à voir.
			mysql_query("DELETE FROM attaque WHERE auteur='".$idAuteur."'");
			//Envoyer un MP pour signifier les résultats.
			//On supprime les unités.
			mysql_query("UPDATE membres SET smack=".$AttSmack.", baiser=".$AttBaiser.", pelle=".$AttPelle.", bloque=0 WHERE id='".$idAuteur."'");
			mysql_query("UPDATE membres SET smack=".$DefSmack.", baiser=".$DefBaiser.", pelle=".$DefPelle." WHERE id='".$idCible."'");
		
			AdminMP($idAuteur,"Quel rateau !!","Bouuhh, t'as perdu tout tes bisous !!
			Tu n'as pas réussi à embrasser ton adversaire !!
			Il te reste :
			- 0 smacks
			- 0 baisers
			- 0 baisers langoureux
			");
			AdminMP($idCible,"Bien esquivé !","Bravo, tu ne t'es pas laissé faire !
			Il te reste :
			- ".$DefSmack." smacks
			- ".$DefBaiser." baisers
			- ".$DefPelle." baisers langoureux
			");
			
			//Bien se défendre fait gagner des points.
			$addScore=5000*($AttScore/$DefScore);
			AjouterScore($idCible,$addScore);
			
		}elseif ($bilan==0)
		{
			$AttSmack=floor($AttSmack*(1 - 1/rand(2,10)));
			$AttBaiser=floor($AttBaiser*(1 - 1/rand(2,10)));
			//Gestion des dents, ca fait plutot mal...
			$dentsCoeff=$DefDent-$AttLangue;
			if ($dentsCoeff<0) {$dentsCoeff=0;};
			$AttPelle=floor($AttPelle*((1 - 1/rand(2,10))*(1 - 0.1*($dentsCoeff))));

			//Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
			if ($DefBloque==0)
			{
				$DefSmack=floor($DefSmack*(1 - 1/rand(2,10)));
				$DefBaiser=floor($DefBaiser*(1 - 1/rand(2,10)));
				$DefPelle=floor($DefPelle*(1 - 1/rand(2,10)));
			}
			
			//Ca retourne, pas de blocage
			mysql_query("UPDATE membres SET smack=".$AttSmack.", baiser=".$AttBaiser.", pelle=".$AttPelle.", WHERE id='".$idAuteur."'");
			mysql_query("UPDATE membres SET smack=".$DefSmack.", baiser=".$DefBaiser.", pelle=".$DefPelle." WHERE id='".$idCible."'");
			
			AdminMP($idAuteur,"Ex Aequo","Egalité parfaite lors de ta dernière tentative.
			Tu ne ramène pas de points d'amour !!
			Il te reste :
			- ".$AttSmack." smacks
			- ".$AttBaiser." baisers
			- ".$AttPelle." baisers langoureux
			");
			AdminMP($idCible,"Ex Aequo","Egalité parfaite contre le joueur qui voulait t'embrasser.
			Il te reste :
			- ".$DefSmack." smacks
			- ".$DefBaiser." baisers
			- ".$DefPelle." baisers langoureux
			");
			
		}elseif ($bilan>0)
		{
			$coeffBilan=$DefForce/$AttForce;
			$AttSmack=floor($AttSmack*(1 - $coeffBilan/rand(2,10)));
			$AttBaiser=floor($AttBaiser*(1 - $coeffBilan/rand(2,10)));
			//Gestion des dents, ca fait plutot mal...
			$dentsCoeff=$DefDent-$AttLangue;
			if ($dentsCoeff<0) {$dentsCoeff=0;};
			$AttPelle=floor($AttPelle*((1 - $coeffBilan/rand(2,10))*(1 - 0.1*($dentsCoeff))));
			//Si les bisous du défenseurs sont présent, donc qu'il n'attaque pas.
			if ($DefBloque==0)
			{
				$DefSmack=floor($DefSmack*($coeffBilan/2));
				$DefBaiser=floor($DefBaiser*($coeffBilan/2));
				$DefPelle=floor($DefPelle*($coeffBilan/2));;
			}
			//Faire retourner, Avec butin.
			

			//Gestion du butin
			if ($idCible==$id && $_SESSION['logged'] == true)
			{
				$DefAmour=$amour;
			}
			else
			{
				$DefTimestamp=$donnees_info4['timestamp'];
				$DefCoeur=$donnees_info4['coeur'];
				$DefAmour=$donnees_info4['amour'];
				$DefAmour=calculterAmour($DefAmour,(time()-$DefTimestamp),$DefCoeur,$DefSmack,$DefBaiser,$DefPelle);
			}
			
			$coeffButin=0.5*($AttApnee - $DefCrachat);
			if ($coeffButin<-1){$coeffButin=-1;}
			$butin=floor((1+ $coeffButin)*($AttSmack*100 + $AttBaiser*1000 + $AttPelle*10000));
			if ($butin<($AttSmack + $AttBaiser*10 + $AttPelle*100)){$butin=($AttSmack + $AttBaiser*10 + $AttPelle*100);}
			if ($butin>floor($DefAmour/2)){$butin=floor($DefAmour/2);}
			$DefAmour-=$butin;
			
			if ($idCible==$id && $_SESSION['logged'] == true)
			{
				$amour=$DefAmour;
			}
			
			//Ca retourne, pas de blocage
			mysql_query("UPDATE membres SET smack=".$AttSmack.", baiser=".$AttBaiser.", pelle=".$AttPelle." WHERE id=".$idAuteur);
			mysql_query("UPDATE membres SET amour=".$DefAmour." ,smack=".$DefSmack.", baiser=".$DefBaiser.", pelle=".$DefPelle." WHERE id=".$idCible);
			
			mysql_query("UPDATE attaque SET butin=".$butin." WHERE auteur=".$idAuteur);
			
			AdminMP($idAuteur,"Tu l'as embrassé !!","Bravo, tu as réussi à embrasser ton adversaire.
			Tes bisous seront bientôt revenus près de toi.
			Tu as réussi à prendre ".$butin." Points d'Amour !!
			Il te reste :
			- ".$AttSmack." smacks
			- ".$AttBaiser." baisers
			- $AttPelle baisers langoureux
			");
			AdminMP($idCible,"Tu t'es fait embrasser","Tu n'as pas su résister à ses Bisous !!
			Tu t'es fait prendre ".$butin." Points d'Amour !!
			Il te reste :
			- ".$DefSmack." smacks
			- ".$DefBaiser." baisers
			- ".$DefPelle." baisers langoureux
			");
			
			//Bien attaquer fait gagner des points.
			$addScore=10000*($DefScore/$AttScore) + ($butin/10);
			AjouterScore($idAuteur,$addScore);
			
		}
	
	}
	
	//Phase retour
	$sql_info = mysql_query("SELECT auteur, butin FROM attaque WHERE finretour<='".time()."'");
	while ($donnees_info = mysql_fetch_assoc($sql_info))
	{
		$idAuteur=$donnees_info['auteur'];
		$butinAuteur=$donnees_info['butin'];
		mysql_query("DELETE FROM attaque WHERE auteur='".$idAuteur."'");

		if ($idAuteur==$id && $_SESSION['logged'] == true)
		{
			$AttAmour=$amour;
		}
		else
		{
			$sql_info3 = mysql_query("SELECT amour FROM membres WHERE id='".$idAuteur."'");
			$donnees_info3 = mysql_fetch_assoc($sql_info3);
			$AttAmour=$donnees_info3['amour'];
		}
		//On fais pas de mise à jour du nb de points d'amour, pas besoin.
		//Récupération des points d'amour.
		$AttAmour+=$butinAuteur;
		
		if ($idAuteur==$id && $_SESSION['logged'] == true)
		{
			$amour=$AttAmour;
			$joueurBloque=0;
		}
		//Libérer l'auteur et ajouter butin
		mysql_query("UPDATE membres SET bloque=0, amour=".$AttAmour." WHERE id='".$idAuteur."'");

	}
}
?>