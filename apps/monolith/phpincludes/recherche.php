<h1>Recherche</h1>
<?php
$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
if (isset($_POST['recherche'])) {
    if (isset($_POST['nomCherche']) && !empty($_POST['nomCherche'])) {
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT
                id,
                pseudo,
                confirmation,
                nuage,
                lastconnect
            FROM membres
            WHERE pseudo = :searched_pseudo
        SQL);
        $stmt->execute([
            'searched_pseudo' => $_POST['nomCherche'],
        ]);
        /**
         * @var array{
         *      id: string, // UUID
         *      pseudo: string,
         *      confirmation: bool,
         *      nuage: int,
         *      lastconnect: string, // ISO 8601 timestamp string
         * }|false $account
         */
        $account = $stmt->fetch();
        if (false !== $account) {
            if (true === $account['confirmation']) {
                $resultat = "<h2>{$account['pseudo']} joue bien sur BisouLand</h2>";
                if ($castToUnixTimestamp->fromPgTimestamptz($account['lastconnect']) > time() - 300) {
                    $resultat .= '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/on.png" alt="Connect&eacute;" title=""/><span>'.$account['pseudo'].' est connect&eacute;</span></a> ';
                } else {
                    $resultat .= '<a class="bulle" style="cursor: default;" onclick="return false;" href=""><img src="images/off.png" alt="Non connect&eacute;" title="" /><span>'.$account['pseudo']." n'est pas connect&eacute;</span></a> ";
                }
                if (true === $blContext['is_signed_in']) {
                    $resultat .= '<a class="bulle" href="'.$account['nuage'].'.nuage.html" >
					<img src="images/nuage.png" title="" alt="" /><span>Nuage : '.$account['nuage'].'</span></a> ';
                }
                $resultat .= '<strong> '.$account['pseudo'].'</strong>';
                if (false === $blContext['is_signed_in']) {
                    $resultat .= '<br /><br />Toi aussi, n\'hesite pas a rejoindre la communaute BisouLand.<br />
					Tu peux t\'inscrire en cliquant <a href="inscription.html" title="S\'inscrire sur BisouLand">ici</a>.';
                }
            } elseif ('bisouland' === strtolower((string) $account['pseudo'])) {
                $resultat = 'BisouLand est notre maitre a tous';
            } else {
                $resultat = "Ce compte existe mais le joueur n'a pas confirme son inscription";
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
