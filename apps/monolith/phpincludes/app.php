<?php

use Bl\Application\Auth\AuthToken\CreateAuthToken;
use Bl\Application\Auth\AuthToken\RemoveAuthToken;
use Bl\Application\Auth\AuthTokenCookie\CreateAuthTokenCookie;
use Bl\Application\Auth\AuthTokenCookie\RemoveAuthTokenCookie;
use Bl\Domain\Auth\AuthToken\TokenHash;
use Bl\Domain\Auth\AuthTokenCookie\Credentials;
use Bl\Domain\Exception\ValidationFailedException;
use Bl\Domain\Upgradable\UpgradableBisou;
use Bl\Domain\Upgradable\UpgradableCategory;
use Bl\Domain\Upgradable\UpgradableOrgan;
use Bl\Domain\Upgradable\UpgradableTechnique;
use Symfony\Component\Uid\Uuid;

header('Content-type: text/html; charset=UTF-8');

// Next step :
// dents acérées --> arracher langue.
// Remplacer bouton annuler par lien annuler au niveau du compteur.
// attaques
// Scores
// Permettre de se déplacer, moyennant des points.

// Attaque : mettre en place la possibilité d'attaquer, avec choix etc..
// Créer système de notification automatique pour avertir.

ob_start();

$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();
$deleteAuthToken = delete_auth_token($pdo);
$saveAuthToken = save_auth_token($pdo);

$inMainPage = true;

/**
 * @var array{
 *      is_signed_in: bool,
 *      account: array{
 *          id: string, // UUID
 *          pseudo: string,
 *          nuage: string,
 *      },
 * } $blContext
 */
$blContext = [
    'is_signed_in' => false,
    'account' => [
        'id' => '00000000-0000-0000-0000-000000000000',
        'pseudo' => 'Not Connected',
        'nuage' => -1,
    ],
];
$resetBlContext = $blContext;

// Front Controller: Handle POST requests
// Handle login
if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['connexion'])) {
    // Ensuite on vérifie que les variables existent et contiennent quelque chose :)
    if (isset($_POST['pseudo'], $_POST['mdp']) && !empty($_POST['pseudo']) && !empty($_POST['mdp'])) {
        // Sélection des informations.
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT id, pseudo, mdp, nuage
            FROM membres
            WHERE pseudo = :pseudo
        SQL);
        $stmt->execute([
            'pseudo' => $_POST['pseudo'],
        ]);
        /**
         * @var array{
         *     id: string, // UUID
         *     pseudo: string,
         *     mdp: string,
         *     nuage: int,
         * }|false $currentAccount
         */
        $currentAccount = $stmt->fetch();

        // Vérifie si le pseudo existe
        if (false !== $currentAccount) {
            // Si le mot de passe est le même.
            if (password_verify($_POST['mdp'], $currentAccount['mdp'])) {
                // --- Persistent authentication
                $createAuthToken = CreateAuthToken::fromRawAccountId(
                    $currentAccount['id'],
                );
                $saveAuthToken->save(
                    $createAuthToken->authToken,
                );

                $blContext = [
                    'is_signed_in' => true,
                    'account' => [
                        'id' => $currentAccount['id'],
                        'pseudo' => $currentAccount['pseudo'],
                        'nuage' => $currentAccount['nuage'],
                    ],
                ];

                $createAuthTokenCookie = CreateAuthTokenCookie::fromCreateAuthToken(
                    $createAuthToken,
                );
                setcookie(
                    $createAuthTokenCookie->getName(),
                    $createAuthTokenCookie->getValue(),
                    $createAuthTokenCookie->getOptions(),
                );
                // ---

                // On supprime le membre non connecté du nombre de visiteurs :
                $stmt = $pdo->prepare(<<<'SQL'
                    DELETE FROM connectbisous
                    WHERE ip = :ip
                SQL);
                $stmt->execute([
                    'ip' => $_SERVER['REMOTE_ADDR'],
                ]);

                // On redirige le membre.
                header('location: cerveau.html');
                exit;
            }
            header('location: connexion.html?e=1');
            exit;
        }
        header('location: connexion.html?e=2');
        exit;
    }
    header('location: connexion.html?e=3');
    exit;
}

$page = (empty($_GET['page'])) ? 'accueil' : htmlentities((string) $_GET['page']);

// Mesures de temps pour évaluer le temps que met la page a se créer.
$temps_debut = microtime_float();

// Check for auth token cookie (persistent authentication)
if (false === $blContext['is_signed_in'] && isset($_COOKIE[Credentials::NAME])) {
    try {
        $credentials = Credentials::fromCookie($_COOKIE[Credentials::NAME]);
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT token_hash, account_id
            FROM auth_tokens
            WHERE auth_token_id = :auth_token_id
              AND expires_at > CURRENT_TIMESTAMP
        SQL);
        $stmt->execute([
            'auth_token_id' => $credentials->authTokenId->toString(),
        ]);
        /** @var array{token_hash: string, account_id: string}|false $authTokenRow */
        $authTokenRow = $stmt->fetch();

        if (false !== $authTokenRow) {
            $tokenHash = TokenHash::fromTokenPlain($credentials->tokenPlain);
            if (hash_equals($authTokenRow['token_hash'], $tokenHash->toString())) {
                // Token is valid, get account details
                $stmt = $pdo->prepare(<<<'SQL'
                    SELECT id, pseudo, nuage
                    FROM membres
                    WHERE id = :account_id
                SQL);
                $stmt->execute([
                    'account_id' => $authTokenRow['account_id'],
                ]);
                /** @var array{id: string, pseudo: string, nuage: int}|false $account */
                $account = $stmt->fetch();

                if (false !== $account) {
                    $blContext = [
                        'is_signed_in' => true,
                        'account' => [
                            'id' => $account['id'],
                            'pseudo' => $account['pseudo'],
                            'nuage' => $account['nuage'],
                        ],
                    ];
                }
            }
        }
    } catch (ValidationFailedException) {
        // Invalid cookie format, ignore silently
    }
}

// Front Controller: Handle logout (via GET parameter)
if ('logout' === $page) {
    if (true === $blContext['is_signed_in']) {
        $removeAuthToken = RemoveAuthToken::fromRawAccountId($blContext['account']['id']);
        $deleteAuthToken->delete($removeAuthToken->accountId);

        $removeAuthTokenCookie = new RemoveAuthTokenCookie();
        setcookie(
            $removeAuthTokenCookie->getName(),
            $removeAuthTokenCookie->getValue(),
            $removeAuthTokenCookie->getOptions(),
        );

        // Redirection.
        header('location: accueil.html');
        exit;
    }
    header('location: connexion.html?e=4');
    exit;
}

// Test en cas de suppression de compte
// @todo ajouter ici une routine de suppression des constructions en cours.
if (isset($_POST['suppr']) && true === $blContext['is_signed_in']) {
    SupprimerCompte($blContext['account']['id']);
    $blContext = $resetBlContext;
}

$temps11 = microtime_float();

// ***************************************************************************
// Si on est connecté
if (true === $blContext['is_signed_in']) {
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            amour,
            bouche, coeur, dent, jambes, langue, oeil,
            baiser, pelle, smack,
            tech1, tech2, tech3, tech4, soupe,
            timestamp, bloque
        FROM membres
        WHERE id = :current_account_id
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /**
     * @var array{
     *      amour: int,
     *      bouche: int, coeur: int, dent: int, jambes: int, langue: int, oeil: int,
     *      baiser: int, pelle: int, smack: int,
     *      tech1: int, tech2: int, tech3: int, tech4: int, soupe: int,
     *      timestamp: string, // ISO 8601 timestamp string
     *      bloque: bool,
     * }|false $currentPlayer
     */
    $currentPlayer = $stmt->fetch();

    // Date du dernier calcul du nombre de points d'amour.
    $lastTime = $castToUnixTimestamp->fromPgTimestamptz($currentPlayer['timestamp']);
    // Temps écoulé depuis le dernier calcul.
    $timeDiff = time() - $lastTime;

    // On récupère le nombre de points d'amour.
    $amour = $currentPlayer['amour'];

    $joueurBloque = $currentPlayer['bloque'];

    $currentPlayerUpgradableLevels = [];
    foreach (UpgradableCategory::cases() as $category) {
        foreach ($category->getCases() as $type) {
            $columnName = $type->toString();
            $currentPlayerUpgradableLevels[$category->value][$type->value] = $currentPlayer[$columnName];
        }
    }

    // Cout en point d'amour pour la construction d'un objet
    // Organes
    $amourE = [];
    $amourE[UpgradableCategory::Organs->value] = [
        expo(
            100,
            0.4,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value],
            1,
        ),
        expo(
            200,
            0.4,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value],
            1,
        ),
        expo(
            250,
            0.4,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Tongue->value],
            1,
        ),
        expo(
            500,
            0.4,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Teeth->value],
            1,
        ),
        expo(
            1000,
            0.6,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value],
            1,
        ),
        expo(
            1000,
            0.4,
            $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Eyes->value],
            1,
        ),
    ];

    // Bisous
    $amourE[UpgradableCategory::Bisous->value] = [
        800,
        3500,
        10000,
    ];

    // Technos
    $amourE[UpgradableCategory::Techniques->value] = [
        expo(1000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::HoldBreath->value], 1),
        expo(2000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Flirt->value], 1),
        expo(3000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Spit->value], 1),
        expo(10000, 0.6, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value], 1),
        expo(5000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
    ];

    // Temps pour la construction de l'objet.
    // Organes
    $tempsE[UpgradableCategory::Organs->value] = [
        ExpoSeuil(235000, 20, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        ExpoSeuil(200000, 25, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        ExpoSeuil(220000, 22, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Tongue->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        ExpoSeuil(210000, 17, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Teeth->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        ExpoSeuil(1000000, 5, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Legs->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        ExpoSeuil(500000, 5, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Eyes->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
    ];

    // Bisous
    $tempsE[UpgradableCategory::Bisous->value] = [
        InvExpo(100, 1.5, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value], 1),
        InvExpo(250, 1.7, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value], 1),
        InvExpo(500, 2, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Mouth->value], 1),
    ];

    // Tech
    $tempsE[UpgradableCategory::Techniques->value] = [
        expo(50, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::HoldBreath->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        expo(1000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Flirt->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        expo(3000, 0.4, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Spit->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        expo(15000, 0.6, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Leap->value] - $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
        expo(5000, 0.3, $currentPlayerUpgradableLevels[UpgradableCategory::Techniques->value][UpgradableTechnique::Soup->value], 1),
    ];

    $amour = calculterAmour($amour, $timeDiff, $currentPlayerUpgradableLevels[UpgradableCategory::Organs->value][UpgradableOrgan::Heart->value], $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value], $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value], $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value]);
    // Mise a jour du nombre de points d'amour, par rapport au temps écoulé.

    // Gestion des pages d'évolution (constructions).
    $evolPage = -1; // Valeur par défaut.
    if ('construction' === $page) {
        $evolPage = 0;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Coeur',
            'Bouche',
            'Langue',
            'Dents',
            'Jambes',
            'Yeux',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Le coeur permet de produire encore et toujours plein d\'amour.<br />
				<span class="info">[ Plus le niveau de Coeur est &eacute;lev&eacute;, plus les Points d\'Amours augmentent rapidement ]<br />
				[ Augmente un peu la distance maximale d\'attaque ]<br />
				[ Augmente le nombre de Points d\'amour d&eacute;pens&eacute;s lors d\'un saut ]</span><br />',

            'La bouche permet de donner des baisers, de plus en plus fougueux.<br />
				<span class="info">[ Acc&eacute;lère la cr&eacute;ation des Bisous ]<br />
				[ Augmente la force des Bisous ]</span><br />',

            'La langue permet de cr&eacute;er de nouveaux bisous, plus efficaces.<br />
				<span class="info">[ Augmente la force des Baisers langoureux ]</span><br />',

            'Avec des dents bien aiguis&eacute;es, personne n\'osera vous approcher.<br />
				<span class="info">[ Augmente la d&eacute;fense lors d\'une agression ]<br />
				[ Augmente les chances de d&eacute;truire des Baisers langoureux ]</span><br />
				',

            'Balade toi dans les nuages !!<br />
				<span class="info">
				[ Augmente beaucoup la distance maximale pour embrasser ]<br />
				[ Augmente un peu la distance maximale de saut ]<br />
				[ Diminue le temps n&eacute;cessaire pour embrasser ]<br />
				[ Diminue le nombre de Points d\'Amour n&eacute;cessaires pour embrasser ]</span><br />',

            'Elle a les yeux r&eacute;volver...<br />
				<span class="info">[ Permet d\'obtenir des informations sur un joueur ]
				[ Chaque niveau augmente les chances d\'obtenir plus d\'information sur un joueur ]<br />
				[ Chaque niveau diminue les chances d\'obtenir plus d\'information sur vous ]</span><br />',
        ];
    } elseif ('bisous' === $page) {
        $evolPage = 1;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Smacks',
            'Baisers',
            'Baisers langoureux',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Pour tenter une première approche.<br />
				<span class="info">[ Le Smack est un Bisou &eacute;conomique, mais qui a peu d\'effets ]</span><br />',
            'A utiliser sans mod&eacute;ration !!<br />
				<span class="info">[ Le Baiser a un bon rapport qualit&eacute;/prix ]<br />
				[ Le Baiser peut prendre 10 fois plus de points d\'amour que le Smack ]</span><br />',
            'Pour ceux qui n\'ont pas peur d\'y aller à fond !!<br />
				<span class="info">[ Beaucoup plus efficace, mais très faible face aux Dents ]<br />
				[ Protège-le contre les Dents en montant le niveau de Langue ]<br />
				[ Le Baiser langoureux peut prendre 10 fois plus de points d\'amour que le Baiser ]</span><br />',
        ];
        if (isset($_POST['suppr_bisous']) && false === $currentPlayer['bloque']) {
            $modif = false;
            foreach (UpgradableBisou::cases() as $bisou) {
                $upgradableItem = $bisou->toString();
                if (isset($_POST['sp'.$upgradableItem]) && $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value] > 0) {
                    $nbSupp = $_POST['sp'.$upgradableItem];
                    if ($nbSupp > 0 && $nbSupp <= $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value]) {
                        $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][$bisou->value] -= $nbSupp;
                        $modif = true;
                    }
                }
            }
            if ($modif) {
                $stmt = $pdo->prepare(<<<'SQL'
                    UPDATE membres
                    SET
                        smack = :peck,
                        baiser = :smooch,
                        pelle = :french_kiss
                    WHERE id = :current_account_id
                SQL);
                $stmt->execute([
                    'peck' => $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Peck->value],
                    'smooch' => $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::Smooch->value],
                    'french_kiss' => $currentPlayerUpgradableLevels[UpgradableCategory::Bisous->value][UpgradableBisou::FrenchKiss->value],
                    'current_account_id' => $blContext['account']['id'],
                ]);
            }
        }
    } elseif ('techno' === $page) {
        $evolPage = 2;
        // Nom de chaque objet d'un type différent.
        $evolNom = [
            'Apn&eacute;e',
            'Flirt',
            'Crachat',
            'Saut',
            'Manger de la soupe',
        ];
        // Description qui accompagne l'objet.
        $evolDesc = [
            'Cette technique permet d\'embrasser plus longtemps.<br />
				<span class="info">[ Augmente le nombre de Points d\'Amour vol&eacute;s ]</span><br />',
            'Permet d\'augmenter vos chances de r&eacute;ussite.<br />
				<span class="info">[ Augmente la force de tes Bisous lorsque tu tentes d\'embrasser quelqu\'un ]</span><br />',
            'C\'est une technique de d&eacute;fense très efficace.<br />
				<span class="info">[ Diminue le nombre de Points d\'Amour que l\'on peut te prendre ]</span><br />',
            'Saute de nuages en nuages !!<br />
				<span class="info">[ Permet de changer de Position et de Nuage ]<br />
				[ Augmente beaucoup la distance maximale de saut ]</span><br />',
            'Pour grandir, il faut manger de la soupe !!<br />
				<span class="info">[ Permet de diminuer le temps de cr&eacute;ation des organes ]<br />
				[ Permet de diminuer le temps de cr&eacute;ation des techniques ]</span><br />',
        ];
    }

    // Si on veut acceder a une des pages d'évolution -> prétraitement.
    if (-1 !== $evolPage) {
        include __DIR__.'/evo.php';
    }

    $stmt = $pdo->prepare(<<<'SQL'
        SELECT COUNT(*) AS total_unread
        FROM notifications
        WHERE (
            account_id = :current_account_id
            AND has_been_read = FALSE
        )
    SQL);
    $stmt->execute([
        'current_account_id' => $blContext['account']['id'],
    ]);
    /** @var array{total_unread: int}|false $results */
    $results = $stmt->fetch();
    if (
        false !== $results
        && $results['total_unread'] > 0
    ) {
        $NewMsgString = $results['total_unread'];
        $NewMsgString .= ' nouvelle'.pluriel($results['total_unread']);
        $NewMsgString .= ' notification'.pluriel($results['total_unread']);
    } else {
        $NewMsgString = 'Pas de nouvelle notification';
    }
}// Fin de partie pour gens connectés.

$temps12 = microtime_float();
// ***************************************************************************
/*Tache de vérification des évolutions.
    Ce code est effectué que l'on soit connecté ou non.
    Chaque fois que l'on demande une page au serveur, ce code est appellé.
    Il permet de créer une sorte de boucle de calcul virtuelle, pour peu qu'il y ait suffisament de gens qui se connectent.
*/
// On récupère les évolutions dont la date de création est atteinte ou dépassée.
$stmt = $pdo->prepare(<<<'SQL'
    SELECT
        id,
        auteur AS account_id,
        classe,
        type,
        cout
    FROM evolution
    WHERE timestamp <= CURRENT_TIMESTAMP
SQL);
$stmt->execute();
/**
 * @var array<int, array{
 *      id: string, // UUID
 *      account_id: string, // UUID
 *      classe: int,
 *      type: int,
 *      cout: int,
 * }> $upgrades
 */
$upgrades = $stmt->fetchAll();
// Boucle qui permet de traiter construction par construction.
foreach ($upgrades as $upgrade) {
    // classe: category of upgradable (organ, technique, bisous)
    // type: category of specific upgradable:
    // * organ: heart, mouth, tongue, etc
    // * technique: spit, jump, etc
    // * bisous: smack, french kiss, etc
    $upgradableCategory = UpgradableCategory::from($upgrade['classe']);
    $upgradableItem = $upgradableCategory->getType($upgrade['type'])->toString();
    // On ajoute le nombre de points d'amour dépensés au score :
    AjouterScore($upgrade['account_id'], $upgrade['cout']);

    // On supprime la construction de la liste des taches.
    $stmt = $pdo->prepare(<<<'SQL'
        DELETE FROM evolution
        WHERE id = :upgrade_id
    SQL);
    $stmt->execute([
        'upgrade_id' => $upgrade['id'],
    ]);

    // On effectue la tache dans la table membre.
    $stmt = $pdo->prepare(<<<SQL
        UPDATE membres
        SET {$upgradableItem} = {$upgradableItem} + 1
        WHERE id = :account_id
        RETURNING amour, {$upgradableItem}
    SQL);
    $stmt->execute([
        'account_id' => $upgrade['account_id'],
    ]);
    /** @var array<string, int>|false $player */
    $player = $stmt->fetch();
    $amourConstructeur = $player['amour'];
    // On récupère l'ancienne valeur.
    $nbObjEvol = $player[$upgradableItem];

    // Si le visiteur est connecté et membre, et si la construction est la sienne, on met a jour les infos sur la page.

    // S'il ya des constructions sur la liste de construction, on relance une construction.
    $stmt = $pdo->prepare(<<<'SQL'
        SELECT
            id AS queued_upgrade_id,
            duree,
            type,
            cout
        FROM liste
        WHERE (
            auteur = :account_id
            AND classe = :classe
        )
        ORDER BY id
        LIMIT 1 OFFSET 0
    SQL);
    $stmt->execute([
        'account_id' => $upgrade['account_id'],
        'classe' => $upgrade['classe'],
    ]);
    /**
     * @var array{
     *      queued_upgrade_id: string, // UUID
     *      duree: int, // in seconds
     *      type: int,
     *      cout: int,
     * }|false $queuedEvolution
     */
    $queuedEvolution = $stmt->fetch();
    if (false !== $queuedEvolution) {
        $timeFin2 = time() + $queuedEvolution['duree'];
        $stmt = $pdo->prepare(<<<'SQL'
            INSERT INTO evolution (id, timestamp, classe, type, auteur, cout)
            VALUES (:upgrade_id, :timestamp, :classe, :type, :account_id, :cout)
        SQL);
        $stmt->execute([
            'upgrade_id' => Uuid::v7(),
            'timestamp' => $castToPgTimestamptz->fromUnixTimestamp($timeFin2),
            'classe' => $upgrade['classe'],
            'type' => $queuedEvolution['type'],
            'account_id' => $upgrade['account_id'],
            'cout' => $queuedEvolution['cout'],
        ]);

        $stmt = $pdo->prepare(<<<'SQL'
            DELETE FROM liste
            WHERE id = :queued_upgrade_id
        SQL);
        $stmt->execute([
            'queued_upgrade_id' => $queuedEvolution['queued_upgrade_id'],
        ]);

        if ($blContext['account']['id'] === $upgrade['account_id']) {
            $currentPlayerUpgradableLevels[$upgrade['classe']][$upgrade['type']] = $nbObjEvol;
            if (1 === $upgrade['classe']) {
                // Bisous: classe = 1
                // $amour -= $queuedEvolution['cout'];
            }
            // Pour l'affichage sur la page en cours.
            if ($evolPage === $upgrade['classe']) {
                $timeFin = $timeFin2;
                $evolution = $upgrade['type'];
            }
        } elseif (1 === $upgrade['classe']) {
            // Bisous: classe = 1
            // $amourConstructeur -= $queuedEvolution['cout'];
            // mysql_query("UPDATE membres SET amour=$amourConstructeur WHERE id={$upgrade['account_id']}");
        }
    } elseif (
        $blContext['account']['id'] === $upgrade['account_id']
        && $evolPage === $upgrade['classe']
    ) {
        $currentPlayerUpgradableLevels[$upgrade['classe']][$upgrade['type']] = $nbObjEvol;
        // Permet a la page de savoir qu'il n'y a plus de construction en cours (pour l'affichage).
        $evolution = -1;
    }
}

// Gestion automatisée des attaques.
include __DIR__.'/attaque.php';

// ***************************************************************************
$temps13 = microtime_float();

// Gestion des différentes pages dispo.
include __DIR__.'/pages.php';

// Si on décide que la page existe.
if (isset($pages[$page])) {
    $title = $pages[$page]['title'].' - Bienvenue sur Bisouland';
    $include = __DIR__.'/'.$pages[$page]['file'];
} else {
    $title = 'Erreur 404 - Bienvenue sur Bisouland';
    $include = __DIR__.'/erreur404.php';
}
$temps31 = microtime_float();

if (false === $blContext['is_signed_in']) {
    $stmt = $pdo->prepare(<<<'SQL'
        INSERT INTO connectbisous (ip, timestamp)
        VALUES (:ip, CURRENT_TIMESTAMP)
        ON CONFLICT (ip) DO UPDATE
        SET timestamp = CURRENT_TIMESTAMP
    SQL);
    $stmt->execute([
        'ip' => $_SERVER['REMOTE_ADDR'],
    ]);
}
$temps32 = microtime_float();

// ETAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes
$stmt = $pdo->prepare(<<<'SQL'
    DELETE FROM connectbisous
    WHERE timestamp < CURRENT_TIMESTAMP - INTERVAL '5 minutes'
SQL);
$stmt->execute();

// Etape 3 : on demande maintenant le nombre de gens connectés.
// Nombre de visiteurs
$stmt = $pdo->query(<<<'SQL'
    SELECT COUNT(*) AS total_connections
    FROM connectbisous
SQL);
/** @var array{total_connections: int} $result */
$result = $stmt->fetch();
$NbVisit = $result['total_connections'];

$stmt = $pdo->prepare(<<<'SQL'
    SELECT COUNT(*) AS total_recent_players
    FROM membres
    WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '5 minutes'
SQL);
$stmt->execute();
/** @var array{total_recent_players: int} $results */
$results = $stmt->fetch();
$NbMemb = $results['total_recent_players'];

$temps14 = microtime_float();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <title>
			<?php echo $title; ?>
		</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" media="screen" type="text/css" title="bisouStyle2" href="includes/bisouStyle2.css" />
		<link rel="shorcut icon" href="/favicon.ico"/>
		<meta name="description" content="<?php echo $title; ?>"/>
		<meta http-equiv="Content-Language" content="fr" />
    </head>

    <body>
<div id="superbig">

	<a href="/" id="Ban"></a>

	<ul id="speedbarre">

            <?php if (true === $blContext['is_signed_in']) { ?>
				<li class="speedgauche">
					<strong><?php echo formaterNombre(floor($amour)); ?></strong> <img src="images/puce.png" title = "Nombre de points d'amour" alt="Nombre de points d'amour" />
				</li>
				<li class="speedgauche">Adoptez la strat&eacute;gie BisouLand !!</li>
				<li class="speeddroite">
                    <a href="logout.html" title="Vous avez termin&eacute; ? D&eacute;connectez-vous !">D&eacute;connexion (<?php echo $blContext['account']['pseudo']; ?>)</a>
				</li>
				<li class="speeddroite">
					<a href="boite.html" title="<?php echo $NewMsgString; ?>"><?php echo $NewMsgString; ?></a>
				</li>
			<?php } else { ?>
                <li class="speedgauche"><a href="connexion.html">Connexion</a></li>
                <li class="speeddroite">Adoptez la strat&eacute;gie BisouLand !! : <a href="inscription.html">Inscription</a></li>
			<?php } ?>

	</ul>
	<div id="pub">
	</div>

	<div id="Menu">
		<div class="sMenu">
            <h3>G&eacute;n&eacute;ral</h3>
			<ul>
				<li><a href="accueil.html">Accueil</a></li>
                <?php if (false === $blContext['is_signed_in']) { ?>
                    <li><a href="inscription.html">Inscription</a></li>
				<?php } ?>
				<li><a href="livreor.html">Livre d'or</a></li>
				<li><a href="stats.html">Statistiques</a></li>
				<li><a href="contact.html">Contact</a></li>
			</ul>
		</div>
    </div>

	<div id="dMenu">
		<div class="sMenu">
            <h3>BisouLand</h3>
			<ul>
                <?php if (true === $blContext['is_signed_in']) { ?>
                    <li><a href="cerveau.html">Cerveau</a></li>
                    <li><a href="construction.html">Organes</a></li>
                    <li><a href="techno.html">Techniques</a></li>
                    <li><a href="bisous.html">Bisous</a></li>
                    <li><a href="nuage.html">Nuages</a></li>
                    <li><a href="boite.html">Notifications</a></li>
                    <li><a href="connected.html">Mon compte</a></li>
				<?php } else { ?>
                    <li>Tu n'es pas connect&eacute;.</li>
                    <li><a href="connexion.html">Connexion</a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="sMenu">
            <h3>Infos</h3>
			<ul>
				<li><a href="faq.html">FAQ</a></li>
				<li><a href="aide.html">Aide</a></li>
                <?php if (true === $blContext['is_signed_in']) { ?>
                    <li><a href="infos.html">Encyclop&eacute;die</a></li>
				<?php } ?>
				<li><a href="topten.html">Top 20</a></li>
				<li><a href="recherche.html">Recherche</a></li>
				<li><a href="membres.html">Joueurs</a></li>
			</ul>
		</div>
    </div>

	<div id="corps">
		<?php
            $temps15 = microtime_float();
include $include;
$temps16 = microtime_float();
?>
	</div>

<?php
    if (true === $blContext['is_signed_in']) {
        $stmt = $pdo->prepare(<<<'SQL'
            UPDATE membres
            SET
                lastconnect = CURRENT_TIMESTAMP,
                timestamp = CURRENT_TIMESTAMP,
                amour = :amour
            WHERE id = :current_account_id
        SQL);
        $stmt->execute([
            'amour' => (int) $amour,
            'current_account_id' => $blContext['account']['id'],
        ]);
    }
?>

	<div id="Bas">
		<p>Il y a actuellement<strong>
<?php
echo $NbVisit.'</strong> visiteur';
if ($NbVisit > 1) {
    echo 's';
}
echo ' et <strong>'.$NbMemb.'</strong> membre';
if ($NbMemb > 1) {
    echo 's';
}
echo ' connect&eacute;';
if ($NbMemb + $NbVisit > 1) {
    echo 's';
}
?> sur BisouLand.</p>
<p><?php
$temps17 = microtime_float();
$temps_fin = microtime_float();
echo '<p class="Tpetit" >Page g&eacute;n&eacute;r&eacute;e en '.round($temps_fin - $temps_debut, 4).' secondes</p>';
/*
echo 'Bench temporaire, ne pas tenir compte :<br />';
echo 'T1 (compte):        '.round($temps12 - $temps11, 4).'<br />';
echo 'T2 (constructions): '.round($temps13 - $temps12, 4).'<br />';
echo 'T3 (pages+nb):      '.round($temps14 - $temps13, 4).'<br />';
echo 'T31 :               '.round($temps31 - $temps13, 4).'<br />';
echo 'T32 :               '.round($temps32 - $temps31, 4).'<br />';
echo 'T33 :               '.round($temps14 - $temps32, 4).'<br />';
echo 'T4 (tete):          '.round($temps15 - $temps14, 4).'<br />';
echo 'T5 (include):       '.round($temps16 - $temps15, 4).'<br />';
echo 'T6 (pied):          '.round($temps17 - $temps16, 4).'<br />';
*/
?>
</p>
		<p class="Tpetit">Tous droits r&eacute;serv&eacute;s &copy; BisouLand - Site respectant les r&egrave;gles de la CNIL</p>

    </div>


</div>
    </body>

</html>
