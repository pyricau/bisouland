<?php

define('DATABASE_HOST', $_ENV['DATABASE_HOST'] ?? 'db');
define('DATABASE_PORT', $_ENV['DATABASE_PORT'] ?? 5432);
define('DATABASE_USER', $_ENV['DATABASE_USER'] ?? 'username');
define('DATABASE_PASSWORD', $_ENV['DATABASE_PASSWORD'] ?? 'password');
define('DATABASE_NAME', $_ENV['DATABASE_NAME'] ?? 'database');
