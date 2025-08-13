<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TestKernel
{
    public static function make(): self
    {
        (new Dotenv())->load(__DIR__.'/../../../monolith/.env');

        $httpClient = HttpClient::createForBaseUri('http://web/');

        // Database connection
        $dbHost = $_ENV['DATABASE_HOST'] ?? 'localhost';
        $dbName = $_ENV['DATABASE_NAME'] ?? '';
        $dbUser = $_ENV['DATABASE_USER'] ?? '';
        $dbPass = $_ENV['DATABASE_PASSWORD'] ?? '';

        if (!\is_string($dbHost) || !\is_string($dbName) || !\is_string($dbUser) || !\is_string($dbPass)) {
            throw new \RuntimeException('Database configuration must be strings');
        }

        $pdo = new \PDO(
            "mysql:host={$dbHost};dbname={$dbName}",
            $dbUser,
            $dbPass
        );

        return new self(
            $httpClient,
            $pdo,
        );
    }

    public function __construct(
        private HttpClientInterface $httpClient,
        private \PDO $pdo,
    ) {
    }

    public function httpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }
}
