<?php
if ($_SESSION['logged'] == true)
{
?>
<h1>Encyclop&eacute;die</h1>
R&eacute;capitulatif te permettant d'orienter ton d&eacute;veloppement.<br />
Attention : ces informations ne sont pas mises &agrave; jour en direct.<br />
Je les donne donc &agrave; titre indicatif, et je les modifierai au fur et &agrave; mesure.<br />
<br />
Tous les 1000 points dépensés dans une cr&eacute;ation (organe, technique, bisous), tu gagnes 1 point de score.<br />
<br />
Lorsque tu annules une cr&eacute;ation, tu ne r&eacute;cup&eacute;res que la  moiti&eacute; des Points d'Amour investi dans sa construction.<br />
<br />
<?php
//*********************************************
//Nom !!
$requis[0]['nomClasse']='Organes';
$requis[0]['nom'] = array(
	'Coeur',
	'Bouche',
	'Langue',
	'Dents',
	'Jambes',
	'Oeil'
);

//Pas de 0 !! (car NbCond=0)
//Bouche
$requis[0][1]['Classe'] = array(
	0 //Organes
);
$requis[0][1]['Type'] = array(
	0 //Coeur
);
$requis[0][1]['Niveau'] = array(
	2
);

//Langue
$requis[0][2]['Classe'] = array(
	0, //Organes
	0
);
$requis[0][2]['Type'] = array(
	0, //coeur
	1 //bouche
);
$requis[0][2]['Niveau'] = array(
	5,
	2
);

//Dents
$requis[0][3]['Classe'] = array(
	0 //Organes
);
$requis[0][3]['Type'] = array(
	1 //bouche
);
$requis[0][3]['Niveau'] = array(
	2
);

//Jambes
$requis[0][4]['Classe'] = array(
	0 //Organes
);
$requis[0][4]['Type'] = array(
	0 //coeur
);
$requis[0][4]['Niveau'] = array(
	15
);

//Oeil
$requis[0][5]['Classe'] = array(
	0 //Organes
);
$requis[0][5]['Type'] = array(
	0 //Coeur
);
$requis[0][5]['Niveau'] = array(
	10
);

//*********************************************
//Nom !!
$requis[1]['nomClasse']='Bisous';
$requis[1]['nom'] = array(
	'Smack',
	'Baiser',
	'Baiser langoureux'
);

//Smack
$requis[1][0]['Classe'] = array(
	0 //Organes
);
$requis[1][0]['Type'] = array(
	1 //bouche
);
$requis[1][0]['Niveau'] = array(
	2
);

//Baiser
$requis[1][1]['Classe'] = array(
	0 //Organes
);
$requis[1][1]['Type'] = array(
	1 //bouche
);
$requis[1][1]['Niveau'] = array(
	6
);

//Baiser langoureux
$requis[1][2]['Classe'] = array(
	0, //Organes
	0 //Organes
);
$requis[1][2]['Type'] = array(
	1, //bouche
	2 //Langue
);
$requis[1][2]['Niveau'] = array(
	10,
	5
);

//*********************************************
//Nom !!
$requis[2]['nomClasse']='Techniques';
$requis[2]['nom'] = array(
	'Apn&eacute;e',
	'Surprise',
	'Crachat',
	'Saut',
	'Manger de la soupe'
);

//Apnée
$requis[2][0]['Classe'] = array(
	0, //Organes
	0 //Organes
);
$requis[2][0]['Type'] = array(
	0, //Coeur
	1 //Bouche
);
$requis[2][0]['Niveau'] = array(
	3,
	2
);

//Surprise
$requis[2][1]['Classe'] = array(
	0, //Organes
	0 //Organes
);
$requis[2][1]['Type'] = array(
	0, //Coeur
	1 //Bouche
);
$requis[2][1]['Niveau'] = array(
	5,
	4
);

//Crachat
$requis[2][2]['Classe'] = array(
	0, //Organes
	2, //Technique
	2 //Technique
);
$requis[2][2]['Type'] = array(
	2, //Langue
	0, //Apnée
	1 //Surprise
);
$requis[2][2]['Niveau'] = array(
	3,
	3,
	3
);

//Saut
$requis[2][3]['Classe'] = array(
	0 //Organes
);
$requis[2][3]['Type'] = array(
	4 //Jambes
);
$requis[2][3]['Niveau'] = array(
	2
);

//Soupe
$requis[2][4]['Classe'] = array(
	0, //Organes
	0, //Organes
	0 //Organes
);
$requis[2][4]['Type'] = array(
	0, //Coeur
	1, //Bouche
	2 //Langue
);
$requis[2][4]['Niveau'] = array(
	15,
	8,
	4
);

for ($c=0;$c<3;$c++)
{
echo '<table width="80%">
	<tr>
		<th width="50%">'.$requis[$c]['nomClasse'].'</th>
		<th >Requis</th>
	</tr>';
	foreach ($requis[$c]['nom'] as $i=>$Nom)
	{
		$nbCond = count($requis[$c][$i]['Niveau']);
		if ($nbCond==0)
		{
			echo '<tr>
					<td>'.$Nom.'</td>
					<td><strong>Pas de conditions</strong></td>';
		}
		else
		{
			if ($nbCond==1)
			{
				echo '<tr>
						<td>'.$Nom.'</td>';
			}
			else
			{
				echo '<tr>
				<td rowspan="'.$nbCond.'" >'.$Nom.'</td>';
			}
			//Affichage des conditions
			foreach ($requis[$c][$i]['Classe'] as $cond=>$classe)
			{
				if ($cond>1){echo '<tr>';}
				if ($nbE[$classe][$requis[$c][$i]['Type'][$cond]]<$requis[$c][$i]['Niveau'][$cond])
				{
					$color='black';
					$reqTexte="Tu n'as pas le niveau requis";
				}
				else
				{
					$color='red';
					$reqTexte='Tu as un niveau suffisant';
				}
				echo '<td>
					<a class="bulle" style="cursor: default;color:'.$color.';" onclick="return false;" href=""><strong>
						' .$requis[$classe]['nom'][$requis[$c][$i]['Type'][$cond]].' niveau '.$requis[$c][$i]['Niveau'][$cond].' 
						</strong><span style="color:'.$color.';">'.$reqTexte.'</span></a>
					</td>
				</tr>';
			}
		}
	}
echo '</table>
<br />';
}
}//logué
else
{
	echo 'Erreur... et vouaip !! :D';
}

?>