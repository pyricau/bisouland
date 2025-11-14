<?php
// Ce qu'on affiche si on est connecté
if (true == $_SESSION['logged']) {
    ?>
<h1>Techniques</h1>
Les techniques vous permettent de mieux vous préparer à faire preuve d'amour.<br />
<?php

    for ($i = 0; $i != $nbType[$evolPage]; ++$i) {
        if (arbre($evolPage, $i, $nbE)) {
            echo '<div class="batiment"><h2>',$evolNom[$i],'<br /></h2>';
            echo $evolDesc[$i],'<br />Niveau actuel : ';
            echo $nbE[$evolPage][$i],'<br />';
            if ($evolution != $i) {
                echo 'Niveau suivant : coute ',formaterNombre($amourE[$evolPage][$i]), " points d'amour<br />";
                echo 'Temps de construction : ',strTemps($tempsE[$evolPage][$i]),'<br />';
            }
            if (-1 == $evolution) {
                if ($amour >= $amourE[$evolPage][$i]) {
                    echo '<form method="post" action="techno.html"><input type="submit"
		name="'.$Obj[$evolPage][$i].'" value="Passer au niveau suivant" /></form>';
                } else {
                    echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[$evolPage][$i] - $amour))." points d'amour pour pouvoir passer au niveau suivant ]</span><br />";
                }
            } elseif ($evolution == $i) {
                ?>
	<script src="includes/compteur.js" type="text/javascript"></script>
	<div id="compteur"><?php echo strTemps($timeFin - time()); ?></div>
	<script language="JavaScript">
		duree="<?php echo $timeFin - time(); ?>";
		stop="";
		fin="Terminé";
		next="Continuer";
		adresseStop="";
		adresseFin="techno.html";
		nbCompteur=1;
		t();
	</script>
	<form method="post" action="techno.html">
		<input type="submit" name="cancel" value="Annuler" />
		<input type="hidden" name="classe" value="2" />
	</form>
	<?php
            }
            ?>
</div>
<?php
        } else {
            echo '<div class="batiment"><h2>',$evolNom[$i],'<br /></h2>';
            echo $evolDesc[$i],'<span class="info">[ --Tu ne remplis pas les conditions requises --]</span><br />
	</div>';
        }
    }
} else {
    echo 'Erreur : Vous vous croyez ou la ??';
    echo '<br />Veuillez vous connecter.';
}
?>
