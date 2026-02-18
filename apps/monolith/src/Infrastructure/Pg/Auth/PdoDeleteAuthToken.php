<?php

declare(strict_types=1);

namespace Bl\Infrastructure\Pg\Auth;

use Bl\Auth\Account\AccountId;
use Bl\Auth\DeleteAuthToken;

final readonly class PdoDeleteAuthToken implements DeleteAuthToken
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
        $this->stmt->execute([
            'account_id' => $accountId->toString(),
        ]);
    }
}
