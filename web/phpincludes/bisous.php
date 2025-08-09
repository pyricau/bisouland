<?php
//Ce qu'on affiche si on est connecté
if ($_SESSION['logged'] == true)
{
?>
<h1>Bisous</h1>
Les Bisous vous permettent d'obtenir de l'amour des autres joueurs<br />
<span class="info">[ Les Bisous ont un coût d'entretien : 1 niveau de Coeur correspond à 3 Smacks, 2 Baisers, ou 1 Baiser langoureux ]</span><br />
<?php
if ($joueurBloque==1)
{
	echo '<br /><span class="info">[ Une action est en cours, tu ne peux pas créer de nouveaux Bisous ]</span><br />';
}

if ($evolution!=-1)
{
?>
<br />
Liste des Bisous en cr&eacute;ation :<br />
<form>
<select size="4">
<?php


	$tempsRestant=$timeFin-time();
	$i=1;
	$nomPrec='';
	$typePrec=0;
	$nbIdent=1;
	$nom = array(
		'Smack',
		'Baiser',
		'Baiser langoureux'
	);
	$nomActuel=$nom[$evolution];
	echo '<option>1) '.$nomActuel.' (en cours)</option>';
	$sql= mysql_query("SELECT type,duree FROM liste WHERE auteur=$id AND classe=$evolPage ORDER BY id");
	
	while($donnees_info = mysql_fetch_assoc($sql))
	{
		$tempsRestant+=$donnees_info['duree'];
		$nomActuel=$nom[$donnees_info['type']];
		if($nomActuel==$nomPrec)
		{
			$nbIdent++;
		}
		else
		{
			if ($nomPrec!='')
			{
			$i++;
			if ($nbIdent!=0)
			{
				if ($typePrec!=2)
				{
					echo '<option>'.$i.') '.$nbIdent.' '.($nomPrec.pluriel($nbIdent)).'</option>';
				}
				else
				{
					echo '<option>'.$i.') '.$nbIdent.' '.$nomPrec.'</option>';
				}
				$nbIdent=1;
				
			}
			else
			{
				echo '<option>'.$i.') '.$nomPrec.'</option>';
			}
			}
		}

		$nomPrec=$nomActuel;
		$typePrec=$donnees_info['type'];
	}
	if($nomPrec!='')
	{
	$i++;
	if ($nbIdent!=0)
	{
		if ($typePrec!=2)
		{
			echo '<option>'.$i.') '.$nbIdent.' '.($nomPrec.pluriel($nbIdent)).'</option>';
		}
		else
		{
			echo '<option>'.$i.') '.$nbIdent.' '.$nomPrec.'</option>';
		}
	}
	else
	{
		echo '<option>'.$i.') '.$nomPrec.'</option>';
	}
	}
	
?>
</select>
</form>
<?php
echo 'Temps total restant : '.strTemps($tempsRestant).'<br />';
}

for($i=0;$i!=$nbType[1];$i++)
{

if (arbre($evolPage,$i,$nbE))
{

	echo '<div class="bisous"><h2>',$evolNom[$i],'<br /></h2>';
	echo $evolDesc[$i],'<br />Nombre disponible : ';
	echo $nbE[1][$i],'<br />';
	
	if ($evolution == $i)
	{
	?>
	<span class="info">[ Ce Bisou est en cours de cr&eacute;ation ]<br /></span>
	<script src="includes/compteur.js" type="text/javascript"></script>
	<div id="compteur"><?php echo strTemps($timeFin-time()); ?></div>
	<script language="JavaScript">
		duree="<?php echo ($timeFin-time());?>";
		stop="Annuler";
		fin="Terminé";
		next="Continuer";
		adresseStop="stop.bisous.html";
		adresseFin="bisous.html";
		nbCompteur=1;
		t();
	</script>
	<?php
	}
	echo 'Nombre de points d\'amour requis pour en créer un : ',formaterNombre($amourE[1][$i]),'<br />';
	echo 'Temps de création : ',strTemps($tempsE[1][$i]),'<br />';
	if ($joueurBloque==0)
	{
		if ($amour>=$amourE[1][$i])
		{
			echo '<form method="post" action="bisous.html"><input type="submit"
			name="'.$Obj[1][$i].'" value="Cr&eacute;er" /></form>';
		}
		else
		{
			echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[1][$i]-$amour)).' points d\'amour pour pouvoir créer ce bisou ]</span><br />';
		}
	}
	
	?>
<?php
echo '</div>';
}
else
{
	echo '<div class="bisous"><h2>',$evolNom[$i],'<br /></h2>';
	echo $evolDesc[$i],'<span class="info">[ --Tu ne remplis pas les conditions requises --]</span><br />

	</div>';

}
}

if (($nbE[1][0]+$nbE[1][1]+$nbE[1][2]>0) && ($joueurBloque==0))
{
?>
<h2>Supprimer des bisous</h2>

<form method="post" action="bisous.html">
    <p>Liquidez vos bisous en trop !!</p>
    <p>
<?php
	for($i=0;$i!=$nbType[1];$i++)	
	{	
		//Si on a des bisous dispo de ce type
		if ($nbE[1][$i]>0)
		{
			echo '<label>',$evolNom[$i],' (max ',$nbE[1][$i],') :<br /><input name="sp',$Obj[1][$i],'" tabindex="',$i,'0" value="0" size="6" /><br />	';
		}		
	}
?>
		<br />
		<input type="submit" tabindex="100" value="Supprimer" name="suppr_bisous"/>
    </p>
</form>

<?php
}//Supprimer
}//Logged
else
{
	echo 'Erreur : Vous vous croyez ou la ??';
	echo '<br />Veuillez vous connecter.';
}
?>
