<?php

	function arbre($classe,$type,$nbE)
	{
		if ($classe==0)
		{
			if ($type==0)
			{
				//coeur
				return true;
			}
			elseif($type==1)
			{
				//bouche
				if ($nbE[0][0]>=2)
				{
					return true;
				}
			}
			elseif($type==2)
			{
				//langue
				if ($nbE[0][1]>=2 && $nbE[0][0]>=5)
				{
					return true;
				}
			}
			elseif($type==3)
			{
				//dent
				if ($nbE[0][1]>=2)
				{
					return true;
				}
			}
			elseif($type==4)
			{
				//jambes
				if ($nbE[0][0]>=15)
				{
					return true;
				}
			}
			elseif($type==5)
			{
				//oeil
				if ($nbE[0][0]>=10)
				{
					return true;
				}
			}
		}
		elseif ($classe==1)
		{
			if ($type==0)
			{
				//smack
				if ($nbE[0][1]>=2)
				{
					return true;
				}
			}
			elseif($type==1)
			{
				//baiser
				if ($nbE[0][1]>=6)
				{
					return true;
				}				
			}
			elseif($type==2)
			{
				//baiser langoureux
				if ($nbE[0][2]>=5 && $nbE[0][1]>=10)
				{
					return true;
				}
			}
		}
		elseif ($classe==2)
		{
			if ($type==0)
			{
				//Apnée
				if ($nbE[0][0]>=3 && $nbE[0][1]>=2)
				{
					return true;
				}
			}
			elseif($type==1)
			{
				//Surprise
				if ($nbE[0][0]>=5 && $nbE[0][1]>=4)
				{
					return true;
				}
			}
			elseif($type==2)
			{
				//Crachat
				if ($nbE[2][0]>=1 && $nbE[2][1]>=3 && $nbE[0][2]>=3)
				{
					return true;
				}
			}
			elseif($type==3)
			{
				//Saut
				if ($nbE[0][4]>=2)
				{
					return true;
				}
			}
			elseif($type==4)
			{
				//Soupe
				if ($nbE[0][0]>=15 && $nbE[0][1]>=8 && $nbE[0][2]>=4)
				{
					return true;
				}
			}
		}
		return false;
	}
	
if (isset($inMainPage) && $inMainPage==true)
{	
				//Nombre de type différents pour la classe concernée.
			$nbEvol=$nbType[$evolPage];
			$evolution = -1; //Valeur par défaut ( = aucune construction en cours).
			
			//Annuler une construction ne permet pas de récupérer les points.
			if (isset($_POST['cancel']) || isset($_GET['cancel']))
			{
				$classeCancel = $evolPage;
				$sql_info = mysql_query("SELECT cout FROM evolution WHERE auteur=".$id." AND classe=".$classeCancel);
				$donnees_info = mysql_fetch_assoc($sql_info);
				$amour += ($donnees_info['cout']/2);
				mysql_query("DELETE FROM evolution WHERE auteur=".$id." AND classe=".$classeCancel);
				
				//On passe à une nouvelle construction si disponible.
				$sql= mysql_query("SELECT id, duree, type, cout FROM liste WHERE auteur=$id AND classe=$classeCancel ORDER BY id LIMIT 0,1");
				if($donnees_info = mysql_fetch_assoc($sql))
				{
					$timeFin2 = time() + $donnees_info['duree'];
					mysql_query("INSERT INTO evolution (id, timestamp, classe, type, auteur, cout) VALUES ('', '".$timeFin2."', $classeCancel, ".$donnees_info['type'].", $id, ".$donnees_info['cout'].")");
					mysql_query("DELETE FROM liste WHERE id=".$donnees_info['id']);

					if ($classeCancel==1)
					{
						//$amour -= $donnees_info['cout'];
					}
				}				
			}
			
			//On détermine s'il y a une construction en cours.
			$sql = mysql_query("SELECT COUNT(*) AS nb_id FROM evolution WHERE auteur='".$id."' AND classe='".$evolPage."'");
			if (mysql_result($sql,0,'nb_id') != 0)
			{
				//Si oui, on récupère les infos sur la construction.
				$sql_info = mysql_query("SELECT timestamp, type FROM evolution WHERE auteur='".$id."' AND classe=".$evolPage."");
				$donnees_info = mysql_fetch_assoc($sql_info);
				//Date a laquelle la construction sera terminée.
				$timeFin = $donnees_info['timestamp'];
				//Type de la construction.
				$evolution = $donnees_info['type'];
				
				//partie qui permet d'ajouter des constructions si il ya déjà des constructions en cours.
				$i=0;
				$stop=0;
				if ($joueurBloque==1 && $evolPage==1) {$stop=1;}
				while($i!=$nbEvol && $stop==0)
				{
					if (isset($_POST[$Obj[$evolPage][$i]]))
					{
						//Pour l'instant, on gère ca que pour les bisous.
						if ($evolPage==1)
						{
							if($amour>=$amourE[$evolPage][$i])
							{
								if (arbre($evolPage,$i,$nbE))
								{
									$sql = mysql_query("SELECT COUNT(*) AS nb_id FROM liste WHERE auteur=$id AND classe=1");
									if (mysql_result($sql,0,'nb_id') < 9)
									{
										//Construction demandée, donc on arrete la boucle.
										$stop=1;
										$dureeConst=$tempsE[$evolPage][$i];
										mysql_query("INSERT INTO liste (id, duree, classe, type, auteur, cout) VALUES ('', $dureeConst, $evolPage, $i, $id, ".$amourE[$evolPage][$i].")");
										//On décrémente le nombre de points d'amour.
										$amour -= $amourE[$evolPage][$i];
									}
								}
							}
						}
					}
					$i++;
				}
				
			}
			else
			{
				//Si rien n'est en construction, on peut construire.
				$i=0;
				$stop=0;
				//On va vérifier pour chaque type d'objet si il ya une demande de construction.
				//Une fois une demande trouvée, on arrete la boucle.
				//Si on est sur la page de construction des Bisous et on attaque, pas de construction possible.
				if ($joueurBloque==1 && $evolPage==1) {$stop=1;}
				while($i!=$nbEvol && $stop==0)
				{
					//On regarde si on a demandé la construction, et si on a le nombre de points d'amour nécessaire.
					//(La vérification du nombre de points d'amour permet d'éviter les tricheurs --> sécurité)
					if (isset($_POST[$Obj[$evolPage][$i]]) && $amour>=$amourE[$evolPage][$i])
					{
						if (arbre($evolPage,$i,$nbE))
						{
							//Construction demandée, donc on arrete la boucle.
							$stop=1;
							//On calcule la date de fin du calcul (servira aussi pour l'affichage sur la page).
							$timeFin = time() + $tempsE[$evolPage][$i];
							//On met l'objet en construction. id non définie car auto incrémentée.
							//Le champ id est peut etre a supprimer.
							mysql_query("INSERT INTO evolution (id, timestamp, classe, type, auteur, cout) VALUES ('', '".$timeFin."', ".$evolPage.", ".$i.", ".$id.", ".$amourE[$evolPage][$i].")");
							//On décrémente le nombre de points d'amour.
							$amour -= $amourE[$evolPage][$i];
							//On indique le type du batiment en construction, pour l'affichage sur la page.
							$evolution = $i;
						}
					}
					//Incrémentation de la boucle.
					$i++;
				}
			}
}
?>