-- BisouLand database schema

-- Player information (associated with Account)
CREATE TABLE IF NOT EXISTS membres (
    id UUID PRIMARY KEY,                -- TODO: rename to account_id
    -- Account
    pseudo VARCHAR(15) NOT NULL,        -- TODO: rename to username
    mdp VARCHAR(255) NOT NULL,          -- TODO: rename to password_hash
    -- Player
    timestamp TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, -- TODO: rename to created_at
    lastconnect TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, -- TODO: rename to last_connected_at
    amour BIGINT DEFAULT 300,          -- TODO: rename to love_points
                                        -- (Game Mechanics: this is the game currency/resource)
    score BIGINT DEFAULT 0,             -- Score for ranking
    nuage INTEGER NOT NULL DEFAULT 1,   -- TODO: rename to cloud_coordinates_x
    position INTEGER NOT NULL DEFAULT 1, -- TODO: rename to cloud_coordinates_y
    bloque BOOLEAN DEFAULT FALSE,       -- TODO: rename to can_leap
                                        -- (Game Mechanics: Players cannot leap if they are blowing kisses)
    -- Upgradables
    ---- Organs
    coeur INTEGER DEFAULT 1,            -- TODO: rename to heart_level
                                        -- (Game Mechanics: generates LovePoints over time)
    bouche INTEGER DEFAULT 1,           -- TODO: rename to mouth_level
    langue INTEGER DEFAULT 0,           -- TODO: rename to tongue_level
    dent INTEGER DEFAULT 0,             -- TODO: rename to teeth_level
    jambes INTEGER DEFAULT 0,           -- TODO: rename to leg_level
                                        -- (Game Mechanics: enables "Leap", which allows to move)
    oeil INTEGER DEFAULT 0,             -- TODO: rename to eyes_level
                                        -- (Game Mechanics: enables "Gaze", which allows to see how many LovePoints someone has)
    ---- Bisous
    smack INTEGER DEFAULT 0,            -- TODO: rename to total_pecks
    baiser INTEGER DEFAULT 0,           -- TODO: rename to total_smooches
    pelle INTEGER DEFAULT 0,            -- TODO: rename to total_french_kisses
    ---- Techniques
    tech1 INTEGER DEFAULT 0,            -- TODO: rename to hold_breath_level
    tech2 INTEGER DEFAULT 0,            -- TODO: rename to flirt_level
    tech3 INTEGER DEFAULT 0,            -- TODO: rename to split_level
    tech4 INTEGER DEFAULT 0,            -- TODO: rename to leap_level
    soupe INTEGER DEFAULT 0,            -- TODO: rename to soup_level

    -- Extra database constraints
    --- Covers:
    --- `WHERE nuage = ?` counting players on a cloud (sign-up cloud assignment)
    --- `WHERE nuage = ? AND position = ?` finding a player at coordinates (gaze/kiss/leap)
    --- `WHERE nuage = ? ORDER BY position ASC` listing all players on a cloud (cloud view page)
    UNIQUE(nuage, position)
);

-- Covers:
-- `WHERE pseudo = ?` login, sign-up duplicate check, player search
-- Enforces case-insensitive uniqueness (prevents "Piwai" and "piwai" as separate accounts)
CREATE UNIQUE INDEX idx_membres_pseudo_lower ON membres(LOWER(pseudo));

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

--------------------------------------------------------------------------------
-- Notifications
-- System-generated messages to inform players about important events
--------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    notification_id UUID PRIMARY KEY,
    account_id UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    received_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    has_been_read BOOLEAN DEFAULT FALSE
);

-- Covers:
-- `WHERE account_id = ? `
-- `WHERE account_id = ? AND notification_id = ?`
-- `WHERE account_id = ? ORDER BY notification_id DESC`
CREATE INDEX idx_notifications_account_id ON notifications(account_id, notification_id DESC);
-- Covers:
-- `WHERE account_id = ? AND has_been_read = ?`
CREATE INDEX idx_notifications_account_read ON notifications(account_id, has_been_read);

-- Visitors tracking via IPs, for stats
CREATE TABLE IF NOT EXISTS connectbisous (
    ip INET PRIMARY KEY,
    timestamp TIMESTAMPTZ NOT NULL   -- Last connection time
);

-- Upgrades currently in progress
CREATE TABLE IF NOT EXISTS evolution (
    id UUID PRIMARY KEY,
    timestamp TIMESTAMPTZ NOT NULL,  -- Completion time
    classe INTEGER NOT NULL,         -- Upgradable Category (Organ, Bisou, Technique)
    type INTEGER NOT NULL,           -- Upgradable Item (Heart, Mouth, Peck, Leap, etc)
    auteur UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,
    cout BIGINT NOT NULL
);

-- Pending Upgrades
CREATE TABLE IF NOT EXISTS liste (
    id UUID PRIMARY KEY,
    auteur UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,
    classe INTEGER NOT NULL,         -- Upgradable Category (Organ, Bisou, Technique)
    type INTEGER NOT NULL,           -- Upgradable Item (Heart, Mouth, Peck, Leap, etc)
    duree INTEGER NOT NULL,
    cout BIGINT NOT NULL
);

-- Guest book v2, for reviews
CREATE TABLE IF NOT EXISTS orbisous (
    id UUID PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMPTZ NOT NULL,
    ip INET NOT NULL
);

-- Articles, for announcements and stuff
CREATE TABLE IF NOT EXISTS newsbisous (
    id UUID PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    timestamp TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Creation time
    timestamp_modification TIMESTAMPTZ DEFAULT NULL           -- Last modification time
);

-- Completed BlownKisses logs, used for 12-hour limit check
CREATE TABLE IF NOT EXISTS logatt (
    id UUID PRIMARY KEY,
    auteur UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE, -- Sender
    cible UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,  -- Receiver
    timestamp TIMESTAMPTZ NOT NULL                                 -- Completion time
);

-- Blown kiss state ENUM type
CREATE TYPE blown_kiss_state AS ENUM ('EnRoute', 'ComingBack', 'CalledOff');

-- BlownKisses currently in progress
CREATE TABLE IF NOT EXISTS attaque (
    auteur UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,  -- Sender ID
    cible UUID NOT NULL REFERENCES membres(id) ON DELETE CASCADE,   -- Receiver ID
    finaller TIMESTAMPTZ NOT NULL,                                  -- Time of arrival
    finretour TIMESTAMPTZ NOT NULL,                                 -- Time of return
    state blown_kiss_state NOT NULL DEFAULT 'EnRoute',
    butin BIGINT DEFAULT 0,                                         -- LovePoints taken if successful
    PRIMARY KEY (auteur, cible)                                     -- A Player can only BlowKisses to just one other Player
);

-- Keeping track on how many Clouds there are
-- TODO: to remove
CREATE TABLE IF NOT EXISTS nuage (
    id UUID PRIMARY KEY,
    nombre INTEGER NOT NULL DEFAULT 0  -- Maximum number of clouds
);

-- Insert default nuage configuration
-- TODO: to remove
INSERT INTO nuage (id, nombre)
VALUES ('00000000-0000-0000-0000-000000000002'::UUID, 1)
ON CONFLICT (id) DO UPDATE SET nombre = nuage.nombre;
