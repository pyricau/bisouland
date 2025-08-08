<?php

// --------------- Etape 1 -----------------
// Si un message est envoy�, on l'enregistre
// -----------------------------------------

if (isset($_POST['message']))
{
    if ($_SESSION['logged'] == true)
	{
		$psd = htmlentities($_SESSION['pseudo']);
        
		$message = htmlentities(addslashes($_POST['message']), ENT_QUOTES); // De m�me pour le message
		$message = nl2br($message); // Pour le message, comme on utilise un textarea, il faut remplacer les Entr�es par des <br />
    
		// On peut enfin enregistrer :o)
		//mysql_query("INSERT INTO orbisous VALUES('', '" . $psd . "', '" . $message . "', '" .time()."')");
	}
}
else
{
$psd="Votre pseudo";
}

// --------------- Etape 2 -----------------
// On �crit les liens vers chacune des pages
// -----------------------------------------

// On met dans une variable le nombre de messages qu'on veut par page
$nombreDeMessagesParPage = 5; // Essayez de changer ce nombre pour voir :o)

// On r�cup�re le nombre total de messages
$retour = mysql_query('SELECT COUNT(*) AS nb_messages FROM orbisous');
$donnees = mysql_fetch_array($retour);
$totalDesMessages = $donnees['nb_messages'];

// On calcule le nombre de pages � cr�er
$nombreDePages  = ceil($totalDesMessages / $nombreDeMessagesParPage);
?>

<h1>Livre d'or</h1>

<?php
    if ($_SESSION['logged'] == true)
	{
?>
<div class=formul>
<form method="post" action="livreor.html">
    <p>Le livre d'or a �t� d�sactiv�, en vue du passage � la v2. Vous pourrez de nouveau poster des messages dans le livre d'or
	d�s que la version 2 de BisouLand sera lanc�e.<br /> <br />
	En attendant, vous pouvez visiter le Livre d'Or de la version 2 ici : <a href="http://bisoutest.piwai.info/livre_or.html">Nouveau Livre d'Or</a></p>
   <?php 
   /*
    <p>
        <label>Message :<br />
        <textarea name="message" tabindex="20" rows="8" cols="35">
Entrez ici votre commentaire.</textarea> <br /></label>
        <input type="submit" tabindex="30" value="Envoyer" />
    </p> */
	?>
</form>
</div>
<?php
	}
?>
	
	
<p>

<center>Il y a actuellement <strong><?php echo $totalDesMessages ?></strong> messages dans le livre d'or.</center>
<br />
<?php


if (isset($_GET['or']))
{
    $or = intval($_GET['or']); // On r�cup�re le num�ro de la page indiqu� dans l'adresse (livreor.php?page=4)
	if ($or>$nombreDePages) {$or=$nombreDePages;}
	elseif ($or<1) {$or=1;}
	}
else // La variable n'existe pas, c'est la premi�re fois qu'on charge la page
{
    $or = 1; // On se met sur la page 1 (par d�faut)
}

// On calcule le num�ro du premier message qu'on prend pour le LIMIT de MySQL
$premierMessageAafficher = ($or - 1) * $nombreDeMessagesParPage;

$reponse = mysql_query('SELECT * FROM orbisous ORDER BY id DESC LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage);

if ($nombreDePages>1) {
echo "<center>Page :";
for ($i = 1 ; $i <= $nombreDePages ; $i++)
{
    if ($i!=$or) {
	echo '<a href="livreor.' . $i . '.html">' . $i . '</a> ';
	}
	else {
	echo ' '.$i.' ';
	}
}
echo '</center><br />';
}

?>

</p>

<?php
while ($donnees = mysql_fetch_array($reponse))
{
?>
<div class=livreor>
<?php
    echo '<p><strong>' . stripslashes($donnees['pseudo']). '</strong> a &eacute;crit le '.date('d/m/Y � H\hi', $donnees['timestamp']).' :<br /><br />' . stripslashes($donnees['message']) . '</p>';
?>
</div>
<?php
}
?>
