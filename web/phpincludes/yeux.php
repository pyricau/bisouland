<?php if ($_SESSION['logged'] == true)
{

	if (isset($_GET['Dnuage'], $_GET['Dpos']) && !empty($_GET['Dnuage']) && !empty($_GET['Dpos']))
	{
		$Dnuage=htmlentities(addslashes($_GET['Dnuage']));
		$Dpos=htmlentities(addslashes($_GET['Dpos']));
		if ($nbE[0][5]>0)
		{
			$sql = mysql_query("SELECT id, oeil, score, pseudo FROM membres WHERE nuage=$Dnuage AND position=$Dpos");
			if($donnees = mysql_fetch_assoc($sql))
			{
				$Did=$donnees['id'];
				$Doeil=$donnees['oeil'];
				$scoreCible=$donnees['score'];
				$pseudoCible=$donnees['pseudo'];
			
				$sql = mysql_query("SELECT score, position, nuage, espion, oeil FROM membres WHERE id=".$id);
				$donnees = mysql_fetch_assoc($sql);
				$scoreSource=$donnees['score'];
				$positionSource=$donnees['position'];
				$nuageSource=$donnees['nuage'];
				$oeilSource=$donnees['oeil'];
				$espionSource=$donnees['espion'];
				
				$scoreCible = floor($scoreCible/1000.);
				$scoreSource = floor($scoreSource/1000.);
				$Niveau=voirNiveau($scoreSource,$scoreCible);
				if($Niveau==0)
				{
					$distance = abs(16*($Dnuage-$nuageSource) + $Dpos - $positionSource);
					$cout=1000*$distance;
					if ($amour>=$cout)
					{
						$amour-=$cout;
						$max=$oeilSource-$Doeil;
						if ($max<0){$max=0;}
						$lvlInfo=rand(0,$max);
						
						AdminMP($Did,"$pseudo t'a d�visag�",$pseudo." vient de te d�visager, et cherche peut-�tre � t'embrasser.");
						
						$resultat="Tu as d�visag� $pseudoCible";

            //Mise � jour des PA de l'espionn� :

						//Note : 
						//coeur, bouche, amour, jambes, smack, baiser, pelle, tech1, tech2, tech3, tech4, dent, langue, bloque, soupe, oeil
						switch ($lvlInfo)
						{
						case 0:
							$resDev='Degr� d\'information : '.$lvlInfo.'/'.$max.'
							
							Malheureusement, tu n\'as pu obtenir aucune information sur '.$pseudoCible.'
							';
						break;
						default:
							$sql = mysql_query("SELECT amour, timestamp, oeil, smack, baiser, pelle, coeur FROM membres WHERE id=$Did");
							$donnees = mysql_fetch_assoc($sql);
						}
						
						if ($lvlInfo>=1)
						{
              $DefAmour = calculterAmour($donnees['amour'],(time()-$donnees['timestamp']),$donnees['coeur'],$donnees['smack'],$donnees['baiser'],$donnees['pelle']);

							$resDev='Degr� d\'information : '.$lvlInfo.'/'.$max.'
							
							'.$pseudoCible.' dispose de : 
							
							'.formaterNombre(floor($DefAmour)).' Points d\'Amour
							
							';
						}
						if ($lvlInfo>=2)
						{
							$resDev.='Un oeil niveau '.$donnees['oeil'].'
							
							';
						}			
						if($lvlInfo>=3)
						{
							$resDev.=$donnees['smack'].' Smack'.pluriel($donnees['smack']).'
							
							';
						}
						if($lvlInfo>=4)
						{
							$resDev.=$donnees['baiser'].' Baiser'.pluriel($donnees['baiser']).'
							
							';
						}
						if($lvlInfo>=5)
						{
							$resDev.=$donnees['pelle'].' Baiser'.pluriel($donnees['pelle']).' langoureux
							
							';
						}
						
						//Envoyer un MP si le user le d�sire.
						if ($espionSource==1 && $lvlInfo!=0)
						{
							AdminMP($id,"Tu as d�visag� $pseudoCible",$resDev,1);
						}
					}
					else
					{
						$resultat="Tu n'as pas assez de Points d'Amour";
					}
				}
				else
				{
					$resultat="Tu n'as pas le m�me niveau que ce joueur";
				}
		
			}
			else
			{
				$resultat="Il n'y a plus de joueur a cette position";
			}
		}
		else
		{
			$resultat="Il te faut des yeux niveau 1 pour d�visager un joueur";
		}
?>
<h1>D�visager</h1>
<br />
<a href="<?php echo $Dnuage;?>.nuage.html">Retourner sur le nuage en cours</a><br />
<br />
<?php
	if (isset($resultat))
	{
		echo '<span class="info">[ '.$resultat.' ]</span><br /><br />';
	}
	if (isset($resDev))
	{
		echo nl2br(htmlentities($resDev));
		if ($lvlInfo!=0)
		{
			if ($espionSource==1)
			{
				echo "Un message t'a �t� envoy� pour enregistrer ces informations.<br />";
			}
			else
			{
				echo "Va dans Mon compte si tu d�sires sauvegarder ces informations dans des messages.<br />";
			}
		}
		if ($amour>=$cout)
		{
			echo '<a href="'.$Dnuage.'.'.$Dpos.'.yeux.html">D�visager '.$pseudoCible.' de nouveau (n�cessite '.$cout.' Points d\'Amour)</a>';
		}

	}
	}
	else
	{
		echo 'Page inaccessible.';
	}
}
else
{
	echo 'Tu n\'es pas connect� !!';
}
