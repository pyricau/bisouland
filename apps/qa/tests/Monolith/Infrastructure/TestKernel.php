<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TestKernel
{
    public static function make(): self
    {
        new Dotenv()->load(__DIR__.'/../../../../monolith/.env');

        $httpClient = HttpClient::createForBaseUri('http://web/');

        // Database connection
        $dbHost = $_ENV['DATABASE_HOST'] ?? 'localhost';
        $dbPort = $_ENV['DATABASE_PORT'] ?? '5432';
        $dbName = $_ENV['DATABASE_NAME'] ?? '';
        $dbUser = $_ENV['DATABASE_USER'] ?? '';
        $dbPass = $_ENV['DATABASE_PASSWORD'] ?? '';

        if (!\is_string($dbHost) || !\is_string($dbPort) || !\is_string($dbName) || !\is_string($dbUser) || !\is_string($dbPass)) {
            throw new \RuntimeException('Database configuration must be strings');
        }

        $pdo = new \PDO(
            "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            $dbUser,
            $dbPass,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_PERSISTENT => true,
            ],
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
