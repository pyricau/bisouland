#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$pdo = bd_connect();

// Get first user from database for testing
$stmt = $pdo->query('SELECT id, bloque FROM membres LIMIT 1');
/**
 * @var array{
 *      id: string, // UUID
 *      bloque: bool,
 * }|false $player
 */
$player = $stmt->fetch();
if (false === $player) {
    throw new Exception('Unexpected: No user found in database for testing');
}

// Returning booleans: works
$selectStmt = $pdo->prepare('SELECT bloque FROM membres WHERE id = :id');

if (is_bool($player['bloque'])) {
    echo "PDO succeeds to return PHP bool for a PostgreSQL BOOLEAN field\n";
}

// Passing booleans: fails
$backupConfirmation = $player['bloque'];
$flippedConfirmation = !$player['bloque'];

$updateStmt = $pdo->prepare('UPDATE membres SET bloque = :bloque WHERE id = :id');

try {
    $updateStmt->execute([
        'bloque' => $flippedConfirmation,
        'id' => $player['id'],
    ]);
} catch (PDOException $pdoException) {
    echo "PDO fails to pass PHP bool as value for a PostgreSQL BOOLEAN field\n";
}

// Passing converted booleans: the working work around
$updateStmt->execute([
    'bloque' => $flippedConfirmation ? 'TRUE' : 'FALSE',
    'id' => $player['id'],
]);
$selectStmt->execute(['id' => $player['id']]);
/**
 * @var array{
 *      bloque: bool,
 * }|false $player
 */
$player = $selectStmt->fetch();
if (false === $player) {
    throw new Exception('Failed to retrieve test user from database');
}

if (is_bool($player['bloque']) && $flippedConfirmation === $player['bloque']) {
    echo "PDO succeeds to pass PHP bool converted as 'TRUE' / 'FALSE' for a PostgreSQL BOOLEAN field\n";
}

// Cleanup
$updateStmt->execute([
    'bloque' => $backupConfirmation ? 'TRUE' : 'FALSE',
    'id' => $player['id'],
]);
$selectStmt->execute(['id' => $player['id']]);
