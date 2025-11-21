#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$pdo = bd_connect();
$castToUnixTimestamp = cast_to_unix_timestamp();
$castToPgTimestamptz = cast_to_pg_timestamptz();

// PDO returns PostgreSQL TIMESTAMPTZ as a PHP string that's in ISO 8601 date format
$selectStmt = $pdo->prepare("SELECT lastconnect FROM membres WHERE pseudo = 'admin'");
$selectStmt->execute();
$player = $selectStmt->fetch();
if (!is_array($player) || !array_key_exists('lastconnect', $player)) {
    throw new Exception('Unexpected: Failed to retrieve admin player from database');
}

if (is_string($player['lastconnect'])) {
    echo "PDO returns PostgreSQL TIMESTAMPTZ as a PHP string that's in ISO 8601 date format:\n";
    echo "  - {$player['lastconnect']}\n";
}

// PDO fails to accept PHP integer (UNIX timestamp)
$backupLastconnect = $player['lastconnect'];
$newLastconnectAsTimestamp = time();

$updateStmt = $pdo->prepare("UPDATE membres SET lastconnect = :lastconnect WHERE pseudo = 'admin'");

try {
    $updateStmt->execute([
        'lastconnect' => $newLastconnectAsTimestamp,
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
]);
$selectStmt->execute();
$player = $selectStmt->fetch();
if (!is_array($player) || !array_key_exists('lastconnect', $player)) {
    throw new Exception('Unexpected: Failed to retrieve admin player from database');
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
]);
