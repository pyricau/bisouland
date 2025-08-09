<?php
header('Content-type: text/html; charset=UTF-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Liste des news</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
        h2, th, td
        {
            text-align:center;
        }
        table
        {
            border-collapse:collapse;
            border:2px solid black;
            margin:auto;
        }
        th, td
        {
            border:1px solid black;
        }
        </style>
    </head>
    
    <body>


<h2><a href="rediger_news.php">Ajouter une news</a></h2>
<p><a href="../index.php">Retourner sur bisouland.piwai.info</a></p>

<?php
	include '../phpincludes/bd.php';
	bd_connect();

//-----------------------------------------------------
// Verification 1 : est-ce qu'on veut poster une news ?
//-----------------------------------------------------

if (isset($_POST['titre']) AND isset($_POST['contenu']))
{
    $titre = addslashes($_POST['titre']);
    $contenu = addslashes($_POST['contenu']);
    // On verifie si c'est une modification de news ou pas
	if ($_POST['id_news'] == -1)
    {
        // Ce n'est pas une modification, on cree une nouvelle entree dans la table
        mysql_query("INSERT INTO newsbisous VALUES('', '" . $titre . "', '" . $contenu . "', '" . time() ."','0')");
    }
    else
    {
        // C'est une modification, on met juste a jour le titre et le contenu
        mysql_query("UPDATE newsbisous SET titre='" . $titre . "', contenu='" . $contenu . "', timestamp_modification='" . time() . "' WHERE id=" . $_POST['id_news']);
    }
}


//--------------------------------------------------------
// Verification 2 : est-ce qu'on veut supprimer une news ?
//--------------------------------------------------------

if (isset($_GET['supprimer_news'])) // Si on demande de supprimer une news
{
    // Alors on supprime la news correspondante
    mysql_query('DELETE FROM newsbisous WHERE id=' . $_GET['supprimer_news']);
}

?>

<table><tr>
<th>Modifier</th>
<th>Supprimer</th>
<th>Titre</th>
<th>Date</th>
</tr>

<?php
$retour = mysql_query('SELECT * FROM newsbisous ORDER BY id DESC');
while ($donnees = mysql_fetch_array($retour)) // On fait une boucle pour lister les news
{
?>

<tr>
<td><?php echo '<a href="rediger_news.php?modifier_news=' . $donnees['id'] . '">'; ?>Modifier</a></td>
<td><?php echo '<a href="liste_news.php?supprimer_news=' . $donnees['id'] . '">'; ?>Supprimer</a></td>
<td><?php echo stripslashes($donnees['titre']); ?></td>
<td><?php echo date('d/m/Y', $donnees['timestamp']); ?></td>
</tr>

<?php
} // Fin de la boucle qui liste les news
// On a fini de travailler, on ferme la connexion :
mysql_close(); // Deconnexion de MySQL
?>
</table>

</body>
</html>
