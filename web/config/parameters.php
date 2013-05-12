<?php

use Symfony\Component\Yaml\Parser;

$yaml = new Parser();

$configPath = __DIR__.'/../../app/config/parameters.yml';
$configFile = file_get_contents($configPath);
$configValues = $yaml->parse($configFile);

define('DATABASE_HOST', $configValues['parameters']['database_host']);
define('DATABASE_USER', $configValues['parameters']['database_user']);
define('DATABASE_PASSWORD', $configValues['parameters']['database_password']);
define('DATABASE_NAME', $configValues['parameters']['database_name']);

define('EMAIL_ADMIN', $configValues['parameters']['email_admin']);
define('EMAIL_EXPEDITOR', $configValues['parameters']['email_expeditor']);
