<?php

declare(strict_types=1);

namespace Bl\Game\PdoPg;

use Bl\Exception\ServerErrorException;
use Bl\Game\SearchUsernames;

final readonly class PdoPgSearchUsernames implements SearchUsernames
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
