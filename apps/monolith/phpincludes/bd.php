<?php

include __DIR__.'/../config/parameters.php';

function bd_connect()
{
    static $pdo = null;

    if (null === $pdo) {
        $dsn = 'mysql:host='.DATABASE_HOST.';port='.DATABASE_PORT.';dbname='.DATABASE_NAME.';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true,
        ];
        $pdo = new PDO($dsn, DATABASE_USER, DATABASE_PASSWORD, $options);
    }

    return $pdo;
}
