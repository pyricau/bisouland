<?php if (true === $blContext['is_signed_in']) { ?>
<h1>Organes</h1>
Les organes vous permettent de vivre votre amour<br />
<?php

    for ($i = 0; $i != $nbType[0]; ++$i) {
        if (arbre($evolPage, $i, $nbE)) {
            echo '<div class="batiment"><h2>',$evolNom[$i],'<br /></h2>';
            echo $evolDesc[$i],'<br />Niveau actuel : ';
            echo $nbE[0][$i],'<br />';
            if ($evolution != $i) {
                echo 'Niveau suivant : coute ',formaterNombre($amourE[0][$i]), " points d'amour<br />";
                echo 'Temps de construction : ',strTemps($tempsE[0][$i]),'<br />';
            }
            if (-1 == $evolution) {
                if ($amour >= $amourE[0][$i]) {
                    echo '<form method="post" action="construction.html"><input type="submit"
		name="'.$Obj[0][$i].'" value="Passer au niveau suivant" /></form>';
                } else {
                    echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[0][$i] - $amour))." points d'amour pour pouvoir passer au niveau suivant ]</span><br />";
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
		adresseFin="construction.html";
		nbCompteur=1;
		t();
	</script>
	<form method="post" action="construction.html">
		<input type="submit" name="cancel" value="Annuler" />
		<input type="hidden" name="classe" value="0" />
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
