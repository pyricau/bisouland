<?php

use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;

if (true === $blContext['is_signed_in']) { ?>
<h1>Organes</h1>
Les organes vous permettent de vivre votre amour<br />
<?php

    foreach (UpgradableOrgan::cases() as $organ) {
        if (arbre($evolPage, $organ->value, $currentPlayerUpgradableLevels)) {
            echo '<div class="batiment"><h2>',$evolNom[$organ->value],'<br /></h2>';
            echo $evolDesc[$organ->value],'<br />Niveau actuel : ';
            echo $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][$organ->value],'<br />';
            if ($organ->value !== $evolution) {
                echo 'Niveau suivant : coute ',formaterNombre($amourE[UpgradableCategory::Organs->value][$organ->value]), " points d'amour<br />";
                echo 'Temps de construction : ',strTemps($tempsE[UpgradableCategory::Organs->value][$organ->value]),'<br />';
            }
            if (-1 === $evolution) {
                if ($amour >= $amourE[UpgradableCategory::Organs->value][$organ->value]) {
                    $upgradableItem = $organ->toString();
                    echo '<form method="post" action="construction.html"><input type="submit"
		name="'.$upgradableItem.'" value="Passer au niveau suivant" /></form>';
                } else {
                    echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[UpgradableCategory::Organs->value][$organ->value] - $amour))." points d'amour pour pouvoir passer au niveau suivant ]</span><br />";
                }
            } elseif ($organ->value === $evolution) {
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
		<input type="hidden" name="classe" value="<?php echo UpgradableCategory::Organs->value; ?>" />
	</form>
	<?php
            }
            ?>
</div>
<?php
        } else {
            echo '<div class="batiment"><h2>',$evolNom[$organ->value],'<br /></h2>';
            echo $evolDesc[$organ->value],'<span class="info">[ --Tu ne remplis pas les conditions requises --]</span><br />
	</div>';
        }
    }
} else {
    echo 'Erreur : Vous vous croyez ou la ??';
    echo '<br />Veuillez vous connecter.';
}
?>
