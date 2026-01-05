<?php

use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableTechnique;

if (true === $blContext['is_signed_in']) { ?>
<h1>Techniques</h1>
Les techniques vous permettent de mieux vous préparer à faire preuve d'amour.<br />
<?php

    foreach (UpgradableTechnique::cases() as $technique) {
        if (arbre($evolPage, $technique->value, $currentPlayerUpgradableLevels)) {
            echo '<div class="batiment"><h2>',$evolNom[$technique->value],'<br /></h2>';
            echo $evolDesc[$technique->value],'<br />Niveau actuel : ';
            echo $currentPlayerUpgradableLevels[$evolPage][$technique->value],'<br />';
            if ($technique->value !== $evolution) {
                echo 'Niveau suivant : coute ',formaterNombre($amourE[$evolPage][$technique->value]), " points d'amour<br />";
                echo 'Temps de construction : ',strTemps($tempsE[$evolPage][$technique->value]),'<br />';
            }
            if (-1 === $evolution) {
                if ($amour >= $amourE[$evolPage][$technique->value]) {
                    $upgradableItem = $technique->toString();
                    echo '<form method="post" action="techno.html"><input type="submit"
		name="'.$upgradableItem.'" value="Passer au niveau suivant" /></form>';
                } else {
                    echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[$evolPage][$technique->value] - $amour))." points d'amour pour pouvoir passer au niveau suivant ]</span><br />";
                }
            } elseif ($technique->value === $evolution) {
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
		<input type="hidden" name="classe" value="<?php echo UpgradableCategory::Techniques->value; ?>" />
	</form>
	<?php
            }
            ?>
</div>
<?php
        } else {
            echo '<div class="batiment"><h2>',$evolNom[$technique->value],'<br /></h2>';
            echo $evolDesc[$technique->value],'<span class="info">[ --Tu ne remplis pas les conditions requises --]</span><br />
	</div>';
        }
    }
} else {
    echo 'Erreur : Vous vous croyez ou la ??';
    echo '<br />Veuillez vous connecter.';
}
?>
