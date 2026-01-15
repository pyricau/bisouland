<?php

declare(strict_types=1);

namespace Bl\Domain\Auth;

use Bl\Domain\Auth\Account\AccountId;
use Bl\Domain\Auth\AuthToken\AuthTokenId;
use Bl\Domain\Auth\AuthToken\ExpiresAt;
use Bl\Domain\Auth\AuthToken\TokenHash;

/**
 * @object-type Entity
 */
final readonly class AuthToken
{
    public function __construct(
        public AuthTokenId $authTokenId,
        public TokenHash $tokenHash,
        public AccountId $accountId,
        public ExpiresAt $expiresAt,
    ) {
    }
}
