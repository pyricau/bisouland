<?php

use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;

if (true === $blContext['is_signed_in']) { ?>
<h1>Bisous</h1>
Les Bisous vous permettent d'obtenir de l'amour des autres joueurs<br />
<span class="info">[ Les Bisous ont un coût d'entretien : 1 niveau de Coeur correspond à 3 Smacks, 2 Baisers, ou 1 Baiser langoureux ]</span><br />
<?php
if (true === $joueurBloque) {
    echo '<br /><span class="info">[ Une action est en cours, tu ne peux pas créer de nouveaux Bisous ]</span><br />';
}

if (-1 !== $evolution) {
    $pdo = bd_connect();
?>
<br />
Liste des Bisous en cr&eacute;ation :<br />
<form>
<select size="4">
<?php

    $tempsRestant = $timeFin - time();
    $i = 1;
    $nomPrec = '';
    $typePrec = 0;
    $nbIdent = 1;
    $nom = [
        'Smack',
        'Baiser',
        'Baiser langoureux',
    ];
    $nomActuel = $nom[$evolution];
    echo '<option>1) '.$nomActuel.' (en cours)</option>';
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            type,
            duree
        FROM liste
        WHERE (
            auteur = :current_account_id
            AND classe = :upgradable_category
        )
        ORDER BY id
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
        'upgradable_category' => $evolPage,
    ]);
    /**
     * @var array<int, array{
     *      type: int,
     *      duree: int,
     * }> $plannedKisses
     */
    $plannedKisses = $stmt->fetchAll();

    foreach ($plannedKisses as $plannedKiss) {
        $tempsRestant += $plannedKiss['duree'];
        $nomActuel = $nom[$plannedKiss['type']];
        if ($nomActuel === $nomPrec) {
            ++$nbIdent;
        } elseif ('' !== $nomPrec) {
            ++$i;
            if (0 !== $nbIdent) {
                if (2 != $typePrec) {
                    echo '<option>'.$i.') '.$nbIdent.' '.($nomPrec.pluriel($nbIdent)).'</option>';
                } else {
                    echo '<option>'.$i.') '.$nbIdent.' '.$nomPrec.'</option>';
                }
                $nbIdent = 1;
            } else {
                echo '<option>'.$i.') '.$nomPrec.'</option>';
            }
        }

        $nomPrec = $nomActuel;
        $typePrec = $plannedKiss['type'];
    }
    if ('' !== $nomPrec) {
    ++$i;
    if (0 !== $nbIdent) {
        if (2 != $typePrec) {
            echo '<option>'.$i.') '.$nbIdent.' '.($nomPrec.pluriel($nbIdent)).'</option>';
        } else {
            echo '<option>'.$i.') '.$nbIdent.' '.$nomPrec.'</option>';
        }
    } else {
        echo '<option>'.$i.') '.$nomPrec.'</option>';
    }
    }

?>
</select>
</form>
<?php
echo 'Temps total restant : '.strTemps($tempsRestant).'<br />';
}

foreach (UpgradableBisou::cases() as $bisou) {
if (arbre($evolPage, $bisou->value, $currentPlayerUpgradableLevels)) {
    echo '<div class="bisous"><h2>',$evolNom[$bisou->value],'<br /></h2>';
    echo $evolDesc[$bisou->value],'<br />Nombre disponible : ';
    echo $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value],'<br />';

    if ($bisou->value === $evolution) {
    ?>
	<span class="info">[ Ce Bisou est en cours de cr&eacute;ation ]<br /></span>
	<script src="includes/compteur.js" type="text/javascript"></script>
	<div id="compteur"><?php echo strTemps($timeFin - time()); ?></div>
	<script language="JavaScript">
		duree="<?php echo $timeFin - time(); ?>";
		stop="Annuler";
		fin="Terminé";
		next="Continuer";
		adresseStop="stop.bisous.html";
		adresseFin="bisous.html";
		nbCompteur=1;
		t();
	</script>
	<?php
    }
    echo 'Nombre de points d\'amour requis pour en créer un : ',formaterNombre($amourE[UpgradableCategory::Bisous->value][$bisou->value]),'<br />';
    echo 'Temps de création : ',strTemps($tempsE[UpgradableCategory::Bisous->value][$bisou->value]),'<br />';
    if (false === $joueurBloque) {
        if ($amour >= $amourE[UpgradableCategory::Bisous->value][$bisou->value]) {
            $upgradableItem = $bisou->toString();
            echo '<form method="post" action="bisous.html"><input type="submit"
			name="'.$upgradableItem.'" value="Cr&eacute;er" /></form>';
        } else {
            echo '<span class="info">[ Il te manque '.formaterNombre(ceil($amourE[UpgradableCategory::Bisous->value][$bisou->value] - $amour)).' points d\'amour pour pouvoir créer ce bisou ]</span><br />';
        }
    }

    ?>
<?php
echo '</div>';
} else {
    echo '<div class="bisous"><h2>',$evolNom[$bisou->value],'<br /></h2>';
    echo $evolDesc[$bisou->value],'<span class="info">[ --Tu ne remplis pas les conditions requises --]</span><br />

	</div>';
}
}

if (
    (
        $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value]
        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value]
        + $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]
        > 0
    )
    && (false === $joueurBloque)
) {
?>
<h2>Supprimer des bisous</h2>

<form method="post" action="bisous.html">
    <p>Liquidez vos bisous en trop !!</p>
    <p>
<?php
    foreach (UpgradableBisou::cases() as $bisou) {
        // Si on a des bisous dispo de ce type
        if ($currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value] > 0) {
            $upgradableItem = $bisou->toString();
            echo '<label>',$evolNom[$bisou->value],' (max ',$currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value],') :<br /><input name="sp',$upgradableItem,'" tabindex="',$bisou->value,'0" value="0" size="6" /><br />	';
        }
    }
?>
		<br />
		<input type="submit" tabindex="100" value="Supprimer" name="suppr_bisous"/>
    </p>
</form>

<?php
}// Supprimer
}// Logged
else {
    echo 'Erreur : Vous vous croyez ou la ??';
    echo '<br />Veuillez vous connecter.';
}
?>
