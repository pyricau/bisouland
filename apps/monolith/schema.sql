-- Basic database schema for Bisouland
-- This creates the minimum tables needed based on the PHP code analysis

-- Members table (main user table)
-- Used throughout the application for user authentication and game state
-- INSERT: inscription.php:62, UPDATE: multiple files (index.php:698, deconnexion.php:13, etc.)
CREATE TABLE IF NOT EXISTS membres (
    id UUID PRIMARY KEY,                   -- User ID (UUIDv7), referenced in all other tables
    pseudo VARCHAR(50) NOT NULL UNIQUE,    -- Username, used in login (redirect.php:21, index.php:60)
    mdp VARCHAR(255) NOT NULL,             -- Password hash, checked in redirect.php:27
    confirmation BOOLEAN DEFAULT FALSE,    -- Account confirmed flag, checked in redirect.php:27
    timestamp TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Account creation time, updated in confirmation.php:92
    lastconnect TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,        -- Last connection time, updated in index.php:698, deconnexion.php:13
    amour BIGINT DEFAULT 1000,             -- Game currency, updated throughout index.php
    nuage INTEGER DEFAULT 1,               -- Cloud/server number, used in nuage positioning
    position INTEGER DEFAULT 1,            -- Position within cloud, used in action.php and nuage.php
    bloque BOOLEAN DEFAULT FALSE,          -- User blocked status, set in action.php:80
    -- Game objects/organs - used in game mechanics (index.php:138, 305)
    coeur INTEGER DEFAULT 1,               -- Heart organ count
    bouche INTEGER DEFAULT 1,              -- Mouth organ count, used in attacks (attaque.php:35, 46)
    langue INTEGER DEFAULT 0,              -- Tongue organ count, used in attacks
    dent INTEGER DEFAULT 0,                -- Teeth organ count, used in attacks
    jambes INTEGER DEFAULT 0,              -- Legs organ count, used in game mechanics
    oeil INTEGER DEFAULT 0,                -- Eyes organ count
    -- Kisses - combat stats used in attaque.php throughout
    smack INTEGER DEFAULT 0,               -- Smack attack points, updated in attaque.php:89, 90
    baiser INTEGER DEFAULT 0,              -- Kiss attack points, updated in attaque.php:89, 90
    pelle INTEGER DEFAULT 0,               -- French kiss attack points, updated in attaque.php:89, 90
    -- Technologies - upgrades, referenced in index.php and attack calculations
    tech1 INTEGER DEFAULT 0,               -- Technology level 1, used in attaque.php:35
    tech2 INTEGER DEFAULT 0,               -- Technology level 2, used in attaque.php:35
    tech3 INTEGER DEFAULT 0,               -- Technology level 3, used in attaque.php:46
    tech4 INTEGER DEFAULT 0,               -- Technology level 4, used in index.php:138
    soupe INTEGER DEFAULT 0,               -- Soup count, used in index.php:138
    score BIGINT DEFAULT 0,                -- Player score for rankings, used in topten.php:37, makeBan.php
    -- Notification and admin fields
    espion BOOLEAN DEFAULT FALSE,          -- Spy mode flag (referenced in PHP)
    newpass VARCHAR(255) DEFAULT NULL      -- New password reset token, set in perdu.php:20
);

--------------------------------------------------------------------------------
-- Authentication Tokens
-- Allows secure Authentication Persistence
--------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS auth_tokens (
    auth_token_id UUID PRIMARY KEY,
    token_hash VARCHAR(64) NOT NULL,
    account_id UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP + '15 days'
);

-- Messages table
-- Field order MUST match INSERT statements in fctIndex.php::AdminMP()
CREATE TABLE IF NOT EXISTS messages (
    id UUID PRIMARY KEY,                -- Message ID (UUIDv7)
    posteur UUID NOT NULL,              -- Sender user ID (foreign key to membres.id)
    destin UUID NOT NULL,               -- Recipient user ID (foreign key to membres.id)
    message TEXT NOT NULL,              -- Matches $message from INSERT
    timestamp TIMESTAMPTZ NOT NULL,     -- Matches $timer/time() from INSERT
    statut BOOLEAN DEFAULT FALSE,       -- Matches '0'/$lu from INSERT (FALSE=unread, TRUE=read)
    titre VARCHAR(100) NOT NULL         -- Matches $titre/$objet from INSERT
);


-- Online users tracking
-- Tracks visitor IPs and connection times, managed in index.php:492-512
CREATE TABLE IF NOT EXISTS connectbisous (
    ip INET PRIMARY KEY,                -- Visitor IP address, from $_SERVER['REMOTE_ADDR']
    timestamp TIMESTAMPTZ NOT NULL,     -- Last activity time, updated in index.php:500
    type SMALLINT DEFAULT 1             -- Connection type (2 for new, 1 for existing)
);

-- Evolution/construction queue
-- Active construction tasks, INSERT in index.php:427, SELECT/DELETE in index.php:392-409
CREATE TABLE IF NOT EXISTS evolution (
    id UUID PRIMARY KEY,                -- Task ID (UUIDv7) for deletion when complete
    timestamp TIMESTAMPTZ NOT NULL,     -- Completion time, checked against time() in index.php:392
    classe INTEGER NOT NULL,            -- Object class/category for construction
    type INTEGER NOT NULL,              -- Specific object type within class
    auteur UUID NOT NULL,               -- User ID (foreign key to membres.id) who initiated construction
    cout BIGINT NOT NULL                -- Cost of the construction task
);

-- Construction queue list
-- Pending construction tasks waiting to start, managed in index.php:423-428
CREATE TABLE IF NOT EXISTS liste (
    id UUID PRIMARY KEY,                -- Queue entry ID (UUIDv7), used for ordering and deletion
    auteur UUID NOT NULL,               -- User ID (foreign key to membres.id) who queued the construction
    classe INTEGER NOT NULL,            -- Object class/category, used to match with evolution
    type INTEGER NOT NULL,              -- Specific object type, used in bisous.php:37
    duree INTEGER NOT NULL,             -- Construction duration in seconds
    cout BIGINT NOT NULL                -- Construction cost, used in bisous.php:37
);

-- Guest book
-- Public guest book entries (livreor = "livre d'or" = golden book)
CREATE TABLE IF NOT EXISTS livreor (
    id UUID PRIMARY KEY,                -- Entry ID (UUIDv7) for ordering and management
    pseudo VARCHAR(50) NOT NULL,        -- Name of guest book signer
    message TEXT NOT NULL,              -- Guest book message content
    timestamp TIMESTAMPTZ NOT NULL,     -- Entry creation time
    ip INET NOT NULL                    -- IP address of the signer
);

-- News (renamed from 'news' to 'newsbisous' to match PHP code)
-- Site news and announcements, managed in news/liste_news.php, displayed in accueil.php:66
CREATE TABLE IF NOT EXISTS newsbisous (
    id UUID PRIMARY KEY,                   -- News article ID (UUIDv7)
    titre VARCHAR(100) NOT NULL,           -- News title, from $titre in liste_news.php:51, 56
    contenu TEXT NOT NULL,                 -- News content, from $contenu in liste_news.php:51, 56
    timestamp TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Creation time, set to time() in liste_news.php:51
    timestamp_modification TIMESTAMPTZ DEFAULT NULL            -- Last modification time, set in liste_news.php:56
);

-- Alternative guest book (orbisous table)
-- Secondary guest book system, similar structure to livreor
CREATE TABLE IF NOT EXISTS orbisous (
    id UUID PRIMARY KEY,                -- Entry ID (UUIDv7) for ordering and management
    pseudo VARCHAR(50) NOT NULL,        -- Name of guest book signer
    message TEXT NOT NULL,              -- Guest book message content
    timestamp TIMESTAMPTZ NOT NULL,     -- Entry creation time
    ip INET NOT NULL                    -- IP address of the signer
);

-- Attack log table
-- Logs completed attacks for rate limiting, INSERT in attaque.php:16, checked in action.php:74
CREATE TABLE IF NOT EXISTS logatt (
    id UUID PRIMARY KEY,                -- Log entry ID (UUIDv7)
    auteur UUID NOT NULL,               -- Attacker user ID (foreign key to membres.id), checked for rate limiting
    cible UUID NOT NULL,                -- Target user ID (foreign key to membres.id)
    timestamp TIMESTAMPTZ NOT NULL      -- Attack completion time, used for 12-hour limit check
);

-- Blown kiss state ENUM type
CREATE TYPE blown_kiss_state AS ENUM ('EnRoute', 'ComingBack', 'CalledOff');

-- Attack table
-- Active attacks in progress, managed throughout attaque.php and action.php
CREATE TABLE IF NOT EXISTS attaque (
    auteur UUID NOT NULL,               -- Attacker user ID (foreign key to membres.id), set bloque=1 during attack
    cible UUID NOT NULL,                -- Target user ID (foreign key to membres.id)
    finaller TIMESTAMPTZ NOT NULL,      -- Attack arrival timestamp (when units reach target)
    finretour TIMESTAMPTZ NOT NULL,     -- Return timestamp (when units return home)
    state blown_kiss_state NOT NULL DEFAULT 'EnRoute',  -- Blown kiss state ENUM
    butin BIGINT DEFAULT 0,             -- Loot gained from attack, set after battle
    PRIMARY KEY (auteur, cible)         -- Composite key: one active attack per attacker-target pair
);

-- Nuage (cloud) configuration table
-- Stores the maximum number of clouds/servers, used in fctIndex.php::GiveNewPosition(), reductionNuages.php:40
CREATE TABLE IF NOT EXISTS nuage (
    id UUID PRIMARY KEY,                -- Config entry ID (singleton)
    nombre INTEGER NOT NULL DEFAULT 0   -- Maximum number of clouds, updated in fctIndex.php::GiveNewPosition()
);

-- Insert default nuage configuration
INSERT INTO nuage (id, nombre) VALUES ('00000000-0000-0000-0000-000000000002'::UUID, 1) ON CONFLICT (id) DO UPDATE SET nombre = nuage.nombre;

-- Insert a default admin user (password: admin, hashed with bcrypt)
INSERT INTO membres (id, pseudo, mdp, confirmation, timestamp, lastconnect)
VALUES ('00000000-0000-0000-0000-000000000001'::UUID, 'admin', '$2y$12$mdsYNRFVDcDCOjXXCfEWG.1jLajEJt/ldCo2kdGS5uBElgyIabRP.', TRUE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT (pseudo) DO NOTHING;
