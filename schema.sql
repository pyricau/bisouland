-- Basic database schema for Bisouland
-- This creates the minimum tables needed based on the PHP code analysis

USE skyswoon;

-- Members table (main user table)
-- Used throughout the application for user authentication and game state
-- INSERT: inscription.php:62, UPDATE: multiple files (index.php:698, deconnexion.php:13, etc.)
CREATE TABLE IF NOT EXISTS membres (
    id INT PRIMARY KEY AUTO_INCREMENT,     -- User ID, referenced in all other tables
    pseudo VARCHAR(50) NOT NULL UNIQUE,    -- Username, used in login (redirect.php:21, index.php:60)
    mdp VARCHAR(255) NOT NULL,             -- Password hash, checked in redirect.php:27
    email VARCHAR(100) NOT NULL,           -- Email address, used in inscription.php and mail notifications
    confirmation TINYINT(1) DEFAULT 0,     -- Account confirmed flag, checked in redirect.php:27
    timestamp INT NOT NULL,                -- Account creation time, updated in confirmation.php:92
    lastconnect INT DEFAULT 0,             -- Last connection time, updated in index.php:698, deconnexion.php:13
    amour BIGINT DEFAULT 1000,             -- Game currency, updated throughout index.php
    nuage INT DEFAULT 1,                   -- Cloud/server number, used in nuage positioning
    position INT DEFAULT 1,                -- Position within cloud, used in action.php and nuage.php
    bloque TINYINT(1) DEFAULT 0,           -- User blocked status, set in action.php:80
    -- Game objects/organs - used in game mechanics (index.php:138, 305)
    coeur INT DEFAULT 1,                   -- Heart organ count
    bouche INT DEFAULT 1,                  -- Mouth organ count, used in attacks (attaque.php:35, 46)
    langue INT DEFAULT 0,                  -- Tongue organ count, used in attacks
    dent INT DEFAULT 0,                    -- Teeth organ count, used in attacks
    jambes INT DEFAULT 0,                  -- Legs organ count, used in game mechanics
    oeil INT DEFAULT 0,                    -- Eyes organ count
    -- Kisses - combat stats used in attaque.php throughout
    smack INT DEFAULT 0,                   -- Smack attack points, updated in attaque.php:89, 90
    baiser INT DEFAULT 0,                  -- Kiss attack points, updated in attaque.php:89, 90
    pelle INT DEFAULT 0,                   -- French kiss attack points, updated in attaque.php:89, 90
    -- Technologies - upgrades, referenced in index.php and attack calculations
    tech1 INT DEFAULT 0,                   -- Technology level 1, used in attaque.php:35
    tech2 INT DEFAULT 0,                   -- Technology level 2, used in attaque.php:35
    tech3 INT DEFAULT 0,                   -- Technology level 3, used in attaque.php:46
    tech4 INT DEFAULT 0,                   -- Technology level 4, used in index.php:138
    soupe INT DEFAULT 0,                   -- Soup count, used in index.php:138
    score BIGINT DEFAULT 0,                -- Player score for rankings, used in topten.php:37, makeBan.php
    -- Notification and admin fields
    averto INT DEFAULT 0,                  -- Warning timestamp, used in checkConnect.php:28, 55
    lastmsg INT DEFAULT 0,                 -- Last message timestamp (referenced in PHP)
    alerte TINYINT(1) DEFAULT 0,           -- Email alert preference, checked in envoi.php:22
    espion TINYINT(1) DEFAULT 0,           -- Spy mode flag (referenced in PHP)
    newpass VARCHAR(255) DEFAULT NULL     -- New password reset token, set in perdu.php:20
);

-- Messages table
-- Field order MUST match INSERT statements in envoi.php:20 and fctIndex.php:144
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Auto-increment, '' in INSERT
    posteur INT NOT NULL,               -- Matches $source/$expediteur from INSERT
    destin INT NOT NULL,                -- Matches $cible from INSERT
    message TEXT NOT NULL,              -- Matches $message from INSERT
    timestamp INT NOT NULL,             -- Matches $timer/time() from INSERT
    statut TINYINT(1) DEFAULT 0,        -- Matches '0'/$lu from INSERT
    titre VARCHAR(100) NOT NULL         -- Matches $titre/$objet from INSERT
);

-- Chat table
-- Stores public chat messages, INSERT in index.php:376, SELECT in index.php:621
CREATE TABLE IF NOT EXISTS chatbisous (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Message ID, used for ordering and deletion
    pseudo VARCHAR(50) NOT NULL,        -- Username of message sender
    message VARCHAR(200) NOT NULL,      -- Chat message content, from $chatmess in index.php:376
    timestamp INT NOT NULL              -- Message timestamp, set to time() in index.php:376
);

-- Online users tracking
-- Tracks visitor IPs and connection times, managed in index.php:492-512
CREATE TABLE IF NOT EXISTS connectbisous (
    ip VARCHAR(15) PRIMARY KEY,         -- Visitor IP address, from $_SERVER['REMOTE_ADDR']
    timestamp INT NOT NULL,             -- Last activity time, updated in index.php:500
    type TINYINT(1) DEFAULT 1           -- Connection type (2 for new, 1 for existing)
);

-- Evolution/construction queue
-- Active construction tasks, INSERT in index.php:427, SELECT/DELETE in index.php:392-409
CREATE TABLE IF NOT EXISTS evolution (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Task ID for deletion when complete
    timestamp INT NOT NULL,             -- Completion time, checked against time() in index.php:392
    classe INT NOT NULL,                -- Object class/category for construction
    type INT NOT NULL,                  -- Specific object type within class
    auteur INT NOT NULL,                -- User ID who initiated construction, from $id2
    cout BIGINT NOT NULL               -- Cost of the construction task
);

-- Construction queue list
-- Pending construction tasks waiting to start, managed in index.php:423-428
CREATE TABLE IF NOT EXISTS liste (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Queue entry ID, used for ordering and deletion
    auteur INT NOT NULL,                -- User ID who queued the construction
    classe INT NOT NULL,                -- Object class/category, used to match with evolution
    type INT NOT NULL,                  -- Specific object type, used in bisous.php:37
    duree INT NOT NULL,                 -- Construction duration in seconds
    cout BIGINT NOT NULL               -- Construction cost, used in bisous.php:37
);

-- Guest book
-- Public guest book entries (livreor = "livre d'or" = golden book)
CREATE TABLE IF NOT EXISTS livreor (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Entry ID for ordering and management
    pseudo VARCHAR(50) NOT NULL,        -- Name of guest book signer
    message TEXT NOT NULL,              -- Guest book message content
    timestamp INT NOT NULL,             -- Entry creation time
    ip VARCHAR(15) NOT NULL            -- IP address of the signer
);

-- News (renamed from 'news' to 'newsbisous' to match PHP code)
-- Site news and announcements, managed in news/liste_news.php, displayed in accueil.php:66
CREATE TABLE IF NOT EXISTS newsbisous (
    id INT PRIMARY KEY AUTO_INCREMENT,     -- News article ID
    titre VARCHAR(100) NOT NULL,           -- News title, from $titre in liste_news.php:51, 56
    contenu TEXT NOT NULL,                 -- News content, from $contenu in liste_news.php:51, 56
    timestamp INT NOT NULL,                -- Creation time, set to time() in liste_news.php:51
    timestamp_modification INT DEFAULT 0   -- Last modification time, set in liste_news.php:56
);

-- Insert a default admin user (password: admin, hashed with md5)
-- Alternative guest book (orbisous table)
-- Secondary guest book system, similar structure to livreor
CREATE TABLE IF NOT EXISTS orbisous (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Entry ID for ordering and management
    pseudo VARCHAR(50) NOT NULL,        -- Name of guest book signer
    message TEXT NOT NULL,              -- Guest book message content
    timestamp INT NOT NULL,             -- Entry creation time
    ip VARCHAR(15) NOT NULL            -- IP address of the signer
);

-- Attack log table
-- Logs completed attacks for rate limiting, INSERT in attaque.php:16, checked in action.php:74
CREATE TABLE IF NOT EXISTS logatt (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Log entry ID
    auteur INT NOT NULL,                -- Attacker user ID, checked for rate limiting
    cible INT NOT NULL,                 -- Target user ID
    timestamp INT NOT NULL              -- Attack completion time, used for 12-hour limit check
);

-- Attack table
-- Active attacks in progress, managed throughout attaque.php and action.php
CREATE TABLE IF NOT EXISTS attaque (
    auteur INT NOT NULL,                -- Attacker user ID, set bloque=1 during attack
    cible INT NOT NULL,                 -- Target user ID
    finaller INT NOT NULL,              -- Attack completion timestamp, checked in attaque.php:7
    fin INT NOT NULL,                   -- Return journey completion timestamp
    etat TINYINT(1) DEFAULT 0,         -- Attack state/phase
    finretour INT DEFAULT 0,            -- Return completion time, checked in attaque.php:220
    butin BIGINT DEFAULT 0             -- Loot gained from attack, set in attaque.php:193
);

-- Nuage (cloud) configuration table
-- Stores the maximum number of clouds/servers, used in confirmation.php:5, reductionNuages.php:40
CREATE TABLE IF NOT EXISTS nuage (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Config entry ID (always 1)
    nombre INT NOT NULL DEFAULT 0       -- Maximum number of clouds, updated in confirmation.php:17
);

-- Ban table (for banned members)
-- Stores banned user IDs, managed in fctIndex.php:332-339, displayed in makeBan.php
CREATE TABLE IF NOT EXISTS ban (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- Ban entry ID
    auteur INT NOT NULL                 -- Banned user ID, INSERT in fctIndex.php:332
);

-- Insert default nuage configuration
INSERT INTO nuage (id, nombre) VALUES (1, 100) ON DUPLICATE KEY UPDATE nombre=nombre;

-- Insert a default admin user (password: admin, hashed with md5)
INSERT INTO membres (pseudo, mdp, email, confirmation, timestamp, lastconnect) 
VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@skyswoon.local', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE pseudo=pseudo;
