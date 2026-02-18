<?php

declare(strict_types=1);

namespace Bl\Infrastructure\Pg\Auth;

use Bl\Auth\AuthToken;
use Bl\Auth\SaveAuthToken;
use Bl\Exception\ValidationFailedException;

final readonly class PdoSaveAuthToken implements SaveAuthToken
{
    private \PDOStatement $stmt;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->stmt = $this->pdo->prepare(<<<'SQL'
            INSERT INTO auth_tokens
            (auth_token_id, token_hash, account_id, expires_at)
            VALUES (:auth_token_id, :token_hash, :account_id, :expires_at)
        SQL);
    }

    /**
     * @throws ValidationFailedException If the account ID does not exist
     */
    public function save(AuthToken $authToken): void
    {
        $this->stmt->execute([
            'auth_token_id' => $authToken->authTokenId->toString(),
            'token_hash' => $authToken->tokenHash->toString(),
            'account_id' => $authToken->accountId->toString(),
            'expires_at' => $authToken->expiresAt->toString(),
        ]);
    }
}
