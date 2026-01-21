#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$pdo = bd_connect();

// Returning booleans: works
$selectStmt = $pdo->prepare("SELECT bloque FROM membres WHERE pseudo = 'admin'");
$selectStmt->execute();
$player = $selectStmt->fetch();
if (!is_array($player) || !array_key_exists('bloque', $player)) {
    throw new Exception('Failed to retrieve admin player from database');
}

if (is_bool($player['bloque'])) {
    echo "PDO succeeds to return PHP bool for a PostgreSQL BOOLEAN field\n";
}

// Passing booleans: fails
$backupConfirmation = $player['bloque'];
$flippedConfirmation = !$player['bloque'];

$updateStmt = $pdo->prepare("UPDATE membres SET bloque = :bloque WHERE pseudo = 'admin'");

try {
    $updateStmt->execute([
        'bloque' => $flippedConfirmation,
    ]);
} catch (PDOException $pdoException) {
    echo "PDO fails to pass PHP bool as value for a PostgreSQL BOOLEAN field\n";
}

// Passing converted booleans: the working work around
$updateStmt->execute([
    'bloque' => $flippedConfirmation ? 'TRUE' : 'FALSE',
]);
$selectStmt->execute();
$player = $selectStmt->fetch();
if (!is_array($player) || !array_key_exists('bloque', $player)) {
    throw new Exception('Failed to retrieve admin player from database');
}

if (is_bool($player['bloque']) && $flippedConfirmation === $player['bloque']) {
    echo "PDO succeeds to pass PHP bool converted as 'TRUE' / 'FALSE' for a PostgreSQL BOOLEAN field\n";
}

// Cleanup
$updateStmt->execute([
    'bloque' => $backupConfirmation ? 'TRUE' : 'FALSE',
]);
$selectStmt->execute();
