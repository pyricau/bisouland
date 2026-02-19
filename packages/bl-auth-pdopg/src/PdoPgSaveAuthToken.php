<?php

declare(strict_types=1);

namespace Bl\Auth\PdoPg;

use Bl\Auth\AuthToken;
use Bl\Auth\SaveAuthToken;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;

final readonly class PdoPgSaveAuthToken implements SaveAuthToken
{
    /** @see https://www.postgresql.org/docs/current/errcodes-appendix.html */
    private const string PG_FOREIGN_KEY_VIOLATION = '23503';

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

    public function save(AuthToken $authToken): void
    {
        try {
            $this->stmt->execute([
                'auth_token_id' => $authToken->authTokenId->toString(),
                'token_hash' => $authToken->tokenHash->toString(),
                'account_id' => $authToken->accountId->toString(),
                'expires_at' => $authToken->expiresAt->toString(),
            ]);
        } catch (\PDOException $pdoException) {
            $accountId = $authToken->accountId->toString();

            match (true) {
                self::PG_FOREIGN_KEY_VIOLATION === $pdoException->getCode()
                    => throw ValidationFailedException::make(
                        "Invalid \"AccountId\" parameter: account does not exist (`{$accountId}` given)",
                        $pdoException,
                    ),
                default => throw ServerErrorException::make(
                    "Failed to SaveAuthToken (`{$accountId}`): unexpected database error",
                    $pdoException,
                ),
            };
        }
    }
}
