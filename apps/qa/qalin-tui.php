#!/usr/bin/env php
<?php

declare(strict_types=1);

// Suppress php-tui's implicit nullable parameter deprecations (PHP 8.5)
error_reporting(\E_ALL & ~\E_DEPRECATED);

use Bl\Qa\Infrastructure\PhpTui\QalinTui;
use Bl\Qa\Infrastructure\Symfony\AppKernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

new Dotenv()->bootEnv(__DIR__.'/.env');

$kernel = new AppKernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

/** @var QalinTui $tui */
$tui = $kernel->getContainer()->get(QalinTui::class);
$tui->run();
