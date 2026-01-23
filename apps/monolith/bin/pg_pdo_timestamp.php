#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();

// Get first user from database for testing
$stmt = $pdo->query('SELECT id, lastconnect FROM membres LIMIT 1');
/**
 * @var array{
 *      id: string, // UUID
 *      lastconnect: string, // ISO 8601 date format
 * }|false $player
 */
$player = $stmt->fetch();
if (false === $player) {
    throw new Exception('Unexpected: No user found in database for testing');
}

// PDO returns PostgreSQL TIMESTAMPTZ as a PHP string that's in ISO 8601 date format
$selectStmt = $pdo->prepare('SELECT lastconnect FROM membres WHERE id = :id');

if (is_string($player['lastconnect'])) {
    echo "PDO returns PostgreSQL TIMESTAMPTZ as a PHP string that's in ISO 8601 date format:\n";
    echo "  - {$player['lastconnect']}\n";
}

// PDO fails to accept PHP integer (UNIX timestamp)
$backupLastconnect = $player['lastconnect'];
$newLastconnectAsTimestamp = time();

$updateStmt = $pdo->prepare('UPDATE membres SET lastconnect = :lastconnect WHERE id = :id');

try {
    $updateStmt->execute([
        'lastconnect' => $newLastconnectAsTimestamp,
        'id' => $player['id'],
    ]);

    echo "Unexpected: PDO succeeds to pass Unix timestamp as value for a PostgreSQL TIMESTAMPTZ field\n";
} catch (PDOException $pdoException) {
    echo "PDO fails to accept PHP integer (UNIX timestamp):\n";
    echo "  - {$newLastconnectAsTimestamp}\n";
}

// PDO accepts PHP string that are in ISO 8601 date format
$newLastconnectAsDate = $castToPgTimestamptz->fromUnixTimestamp($newLastconnectAsTimestamp);
$updateStmt->execute([
    'lastconnect' => $newLastconnectAsDate,
    'id' => $player['id'],
]);
$selectStmt->execute(['id' => $player['id']]);
/**
 * @var array{
 *      lastconnect: string, // ISO 8601 date format
 * }|false $player
 */
$player = $selectStmt->fetch();
if (false === $player) {
    throw new Exception('Unexpected: Failed to retrieve test user from database');
}

if ($newLastconnectAsTimestamp === $castToUnixTimestamp->fromPgTimestamptz($player['lastconnect'])) {
    echo "PDO accepts PHP string that are in ISO 8601 date format:\n";
    echo "  - PHP integer (UNIX timestamp): {$newLastconnectAsTimestamp}\n";
    echo "  - PHP string (ISO 8601 date format): {$newLastconnectAsDate}\n";
    echo "  - Returned by PostgreSQL: {$player['lastconnect']}\n";
}

// Cleanup
$updateStmt->execute([
    'lastconnect' => $backupLastconnect,
    'id' => $player['id'],
]);
