<?php

header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Rédiger une news</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
        h3, form
        {
            text-align:center;
        }
        </style>
    </head>

    <body>

<h3><a href="liste_news.php">Retour à la liste des news</a></h3>

<?php

    include __DIR__.'/../phpincludes/bd.php';
$pdo = bd_connect();

if (isset($_GET['modifier_news'])) { // Si on demande de modifier une news
    // On récupère les infos de la correspondante
    $stmt = $pdo->prepare('SELECT * FROM newsbisous WHERE id = :id');
    $stmt->execute(['id' => $_GET['modifier_news']]);
    $donnees = $stmt->fetch();

    // On place le titre et le contenu dans des variables simples
    $titre = $donnees['titre'];
    $contenu = stripslashes((string) $donnees['contenu']);
    $id_news = $donnees['id']; // Cette variable va servir pour se souvenir que c'est une modification
} else { // C'est qu'on rédige une nouvelle news
    // Les variables $titre et $contenu sont vides, puisque c'est une nouvelle news
    $titre = '';
    $contenu = '';
    $id_news = -1; // La variable vaut -1, donc on se souviendra que ce n'est pas une modification
}

?>

<form action="liste_news.php" method="post">
<p>Titre : <input type="text" size="30" name="titre" value="<?php echo $titre; ?>" /></p>

<p>
    Contenu :<br />
    <textarea name="contenu" cols="50" rows="10"><?php echo $contenu; ?></textarea><br />

    <input type="hidden" name="id_news" value="<?php echo $id_news; ?>" />
    <input type="submit" value="Envoyer" />
</p>
</form>

</body>
</html>
