<h1>Recherche</h1>
<?php
if (isset($_POST['recherche'])) {
    if (isset($_POST['nomCherche']) && !empty($_POST['nomCherche'])) {
        $pseudoCherche = htmlentities(addslashes($_POST['nomCherche']));
        $sql_info = mysql_query("SELECT id, pseudo, confirmation, nuage, lastconnect FROM membres WHERE pseudo='$pseudoCherche'");
        if ($donnees = mysql_fetch_assoc($sql_info)) {
            $pseudoCherche = $donnees['pseudo'];
            if ($donnees['confirmation'] == 1) {
                $resultat = "<h2>$pseudoCherche joue bien sur BisouLand</h2>";

                if ($donnees['lastconnect'] > time() - 300) {
                    $resultat .= '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>'.$pseudoCherche.' est connect&eacute;</span></a> ';
                } else {
                    $resultat .= '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>'.$pseudoCherche.' n\'est pas connect&eacute;</span></a> ';
                }
                if ($_SESSION['logged'] == true) {
                    if ($donnees['id'] != $id) {
                        $resultat .= '<a class="bulle" href="'.$pseudoCherche.'.envoi.html" >
						<img src="images/mess.png" title="" alt="" /><span>Envoyer un message a '.$pseudoCherche.'</span></a> ';
                    }
                    $resultat .= '<a class="bulle" href="'.$donnees['nuage'].'.nuage.html" >
					<img src="images/nuage.png" title="" alt="" /><span>Nuage : '.$donnees['nuage'].'</span></a> ';
                }
                $resultat .= '<strong> '.$pseudoCherche.'</strong>';
                if ($_SESSION['logged'] == false) {
                    $resultat .= '<br /><br />Toi aussi, n\'hesite pas a rejoindre la communaute BisouLand.<br />
					Tu peux t\'inscrire en cliquant <a href="inscription.html" title="S\'inscrire sur BisouLand">ici</a>.';
                }
            } else {
                if (strtolower($pseudoCherche) == "bisouland") {
                    $resultat = "BisouLand est notre maitre a tous";
                } else {
                    $resultat = "Ce compte existe mais le joueur n'a pas confirme son inscription";
                }
            }
        } else {
            $resultat = "Ce joueur n'existe pas";
        }
    }
    if (isset($resultat)) {
        echo $resultat.'<br /><br />';
    }
}
?>
<form method="post" action="recherche.html">
	<input type="text" name="nomCherche" maxlength="15" size="15" value="" tabindex="20"/>
	<input type="submit" name="recherche" tabindex="30" value="Chercher" />
</form>