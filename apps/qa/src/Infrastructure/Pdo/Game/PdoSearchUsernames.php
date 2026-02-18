<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Pdo\Game;

use Bl\Exception\ServerErrorException;
use Bl\Qa\Domain\Game\SearchUsernames;

final readonly class PdoSearchUsernames implements SearchUsernames
{
    private \PDOStatement $searchStmt;

    public function __construct(
        \PDO $pdo,
    ) {
        $this->searchStmt = $pdo->prepare(<<<'SQL'
            SELECT pseudo AS username
            FROM membres
            WHERE pseudo ILIKE :query
            ORDER BY pseudo
            LIMIT 10
        SQL);
    }

    /**
     * @return list<string>
     */
    public function search(string $query): array
    {
        try {
            $this->searchStmt->execute([
                'query' => "{$query}%",
            ]);
        } catch (\PDOException $pdoException) {
            throw ServerErrorException::make(
                "Failed to SearchUsernames (`{$query}`): unexpected database error",
                $pdoException,
            );
        }

        /** @var list<array{username: string}> $rows */
        $rows = $this->searchStmt->fetchAll();

        return array_column($rows, 'username');
    }
}
