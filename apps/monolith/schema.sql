-- BisouLand database schema

-- Player information (associated with Account)
CREATE TABLE IF NOT EXISTS membres (
    id UUID PRIMARY KEY,
    -- Account
    pseudo VARCHAR(50) NOT NULL UNIQUE, -- aka pseudonym
    mdp VARCHAR(255) NOT NULL,          -- aka password_hash
    newpass VARCHAR(255) DEFAULT NULL,  -- For lost password @TODO remove
    -- Player
    timestamp TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, -- aka created_at
    lastconnect TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, -- aka last_connected_at
    amour BIGINT DEFAULT 1000,          -- aka total_love_points
                                        -- (Game Mechanics: this is the game currency/resource)
    score BIGINT DEFAULT 0,             -- Score for ranking
    nuage INTEGER DEFAULT 1,            -- aka cloud_coordinates_x
    position INTEGER DEFAULT 1,         -- aka cloud_coordinates_y
    bloque BOOLEAN DEFAULT FALSE,       -- aka can_leap
                                        -- (Game Mechanics: Players cannot leap if they are blowing kisses)
    espion BOOLEAN DEFAULT FALSE,       -- aka keep_gaze_journal
                                        -- (Game Mechanics: Players can get spy reports on other Players)
    -- Upgradables
    ---- Organs
    coeur INTEGER DEFAULT 1,            -- aka heart_level
                                        -- (Game Mechanics: generates LovePoints over time)
    bouche INTEGER DEFAULT 1,           -- aka mouth_level
    langue INTEGER DEFAULT 0,           -- aka tongue_level
    dent INTEGER DEFAULT 0,             -- aka teeth_level
    jambes INTEGER DEFAULT 0,           -- aka leg_level
                                        -- (Game Mechanics: enables "Leap", which allows to move)
    oeil INTEGER DEFAULT 0,             -- aka eyes_level
                                        -- (Game Mechanics: enables "Gaze", which allows to see how many LovePoints someone has)
    ---- Bisous
    smack INTEGER DEFAULT 0,            -- aka total_pecks
    baiser INTEGER DEFAULT 0,           -- aka total_smooches
    pelle INTEGER DEFAULT 0,            -- aka total_french_kisses
    ---- Techniques
    tech1 INTEGER DEFAULT 0,            -- aka hold_breath_level
    tech2 INTEGER DEFAULT 0,            -- aka flirt_level
    tech3 INTEGER DEFAULT 0,            -- aka split_level
    tech4 INTEGER DEFAULT 0,            -- aka leap_level
    soupe INTEGER DEFAULT 0             -- aka soup_level
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
    timestamp TIMESTAMPTZ NOT NULL,  -- Last connection time
    type SMALLINT DEFAULT 1          -- Connection type (2 for new, 1 for existing) @TODO remove
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
CREATE TABLE IF NOT EXISTS nuage (
    id UUID PRIMARY KEY,
    nombre INTEGER NOT NULL DEFAULT 0  -- Maximum number of clouds
);

-- Insert default nuage configuration
INSERT INTO nuage (id, nombre)
VALUES ('00000000-0000-0000-0000-000000000002'::UUID, 1)
ON CONFLICT (id) DO UPDATE SET nombre = nuage.nombre;

-- Insert a default admin user (password: admin, hashed with bcrypt)
INSERT INTO membres (id, pseudo, mdp, timestamp, lastconnect)
VALUES (
    '00000000-0000-0000-0000-000000000001'::UUID,
    'admin',
    '$2y$12$mdsYNRFVDcDCOjXXCfEWG.1jLajEJt/ldCo2kdGS5uBElgyIabRP.',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
)
ON CONFLICT (pseudo) DO NOTHING;
