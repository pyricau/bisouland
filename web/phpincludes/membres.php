<h1>Liste des joueurs</h1>
<?php
$sql = mysql_query("SELECT COUNT(*) AS nb_pseudo FROM membres WHERE confirmation='1'");

$total=mysql_result($sql,0,'nb_pseudo');

echo 'Nombre de membres : '.$total.'<br /><br />';

$nombreParPage = 15;

// On calcule le nombre de pages à créer
$nombreDePages  = ceil($total / $nombreParPage);

if (isset($_GET['num']))
{
    $num = intval($_GET['num']);
	if ($num>$nombreDePages) {$num=$nombreDePages;}
	elseif ($num<1) {$num=1;}
	}
else // La variable n'existe pas, c'est la première fois qu'on charge la page
{
    $num = 1; // On se met sur la page 1 (par défaut)
}

// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premier = ($num - 1) * $nombreParPage;


$retour = mysql_query('SELECT id, pseudo, nuage, lastconnect FROM membres WHERE confirmation=1 ORDER BY id DESC LIMIT ' . $premier . ', ' . $nombreParPage);

if ($nombreDePages>1) {
echo "<center>Page :";
for ($i = 1 ; $i <= $nombreDePages ; $i++)
{
    if ($i!=$num)
	{
		echo '<a href="membres.' . $i . '.html">' . $i . '</a> ';
	}
	else
	{
		echo ' '.$i.' ';
	}
}
echo '</center><br />';
}

if ($_SESSION['logged'] == true)
{
	while ($donnees = mysql_fetch_assoc($retour))
	{
		$donnees['pseudo']=stripslashes($donnees['pseudo']);
		if ($donnees['lastconnect']>time()-300)
		{
			echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$donnees['pseudo'],' est connect&eacute;</span></a> ';
		}
		else
		{
			echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$donnees['pseudo'],' n\'est pas connect&eacute;</span></a> ';
		}		
		if ($donnees['id']!=$id)
		{
			echo '<a class="bulle" href="',$donnees['pseudo'],'.envoi.html" >
			<img src="images/mess.png" title="" alt="" /><span>Envoyer un message à '.$donnees['pseudo'].'</span></a> ';
		}
		echo '<a class="bulle" href="',$donnees['nuage'],'.nuage.html" >
		<img src="images/nuage.png" title="" alt="" /><span>Nuage : ',$donnees['nuage'],'</span></a>
		<strong> ',$donnees['pseudo'],'</strong>
		<br />';
	}
}
else
{
	while ($donnees = mysql_fetch_assoc($retour))
	{
		$donnees['pseudo']=stripslashes($donnees['pseudo']);
		if ($donnees['lastconnect']>time()-300)
		{
			echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>',$donnees['pseudo'],' est connect&eacute;</span></a> ';
		}
		else
		{
			echo '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>',$donnees['pseudo'],' n\'est pas connect&eacute;</span></a> ';
		}		
		echo '<strong>'.$donnees['pseudo'].'</strong><br />';
	}
}
?>