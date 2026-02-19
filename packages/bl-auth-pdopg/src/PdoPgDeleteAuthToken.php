<?php

declare(strict_types=1);

namespace Bl\Auth\PdoPg;

use Bl\Auth\Account\AccountId;
use Bl\Auth\DeleteAuthToken;
use Bl\Exception\ServerErrorException;

final readonly class PdoPgDeleteAuthToken implements DeleteAuthToken
{
    private \PDOStatement $stmt;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->stmt = $this->pdo->prepare(<<<'SQL'
            DELETE FROM auth_tokens
            WHERE account_id = :account_id
        SQL);
    }

    public function delete(AccountId $accountId): void
    {
        try {
            $this->stmt->execute([
                'account_id' => $accountId->toString(),
            ]);
        } catch (\PDOException $pdoException) {
            throw ServerErrorException::make(
                "Failed to DeleteAuthToken (`{$accountId->toString()}`): unexpected database error",
                $pdoException,
            );
        }
    }
}
