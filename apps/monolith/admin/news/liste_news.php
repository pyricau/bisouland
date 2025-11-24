<?php

use Symfony\Component\Uid\Uuid;

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
    include __DIR__.'/../../phpincludes/bd.php';
$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();

// -----------------------------------------------------
// Verification 1 : est-ce qu'on veut poster une news ?
// -----------------------------------------------------

if (isset($_POST['titre']) && isset($_POST['contenu'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    // On verifie si c'est une modification de news ou pas
    if (-1 == $_POST['id_news']) {
        // Ce n'est pas une modification, on cree une nouvelle entree dans la table
        $stmt = $pdo->prepare('INSERT INTO newsbisous (id, titre, contenu, timestamp) VALUES(:id, :titre, :contenu, CURRENT_TIMESTAMP)');
        $stmt->execute(['id' => Uuid::v7(), 'titre' => $titre, 'contenu' => $contenu]);
    } else {
        // C'est une modification, on met juste a jour le titre et le contenu
        $stmt = $pdo->prepare('UPDATE newsbisous SET titre = :titre, contenu = :contenu, timestamp_modification = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['titre' => $titre, 'contenu' => $contenu, 'id' => $_POST['id_news']]);
    }
}

// --------------------------------------------------------
// Verification 2 : est-ce qu'on veut supprimer une news ?
// --------------------------------------------------------

if (isset($_GET['supprimer_news'])) { // Si on demande de supprimer une news
    // Alors on supprime la news correspondante
    $stmt = $pdo->prepare('DELETE FROM newsbisous WHERE id = :id');
    $stmt->execute(['id' => $_GET['supprimer_news']]);
}

?>

<table><tr>
<th>Modifier</th>
<th>Supprimer</th>
<th>Titre</th>
<th>Date</th>
</tr>

<?php
$retour = $pdo->query('SELECT * FROM newsbisous ORDER BY id DESC');
while ($donnees = $retour->fetch()) { // On fait une boucle pour lister les news
    ?>

<tr>
<td><?php echo '<a href="rediger_news.php?modifier_news='.$donnees['id'].'">'; ?>Modifier</a></td>
<td><?php echo '<a href="liste_news.php?supprimer_news='.$donnees['id'].'">'; ?>Supprimer</a></td>
<td><?php echo stripslashes((string) $donnees['titre']); ?></td>
<td><?php echo date('d/m/Y', $castToUnixTimestamp->fromPgTimestamptz($donnees['timestamp'])); ?></td>
</tr>

<?php
} // Fin de la boucle qui liste les news
?>
</table>

</body>
</html>
