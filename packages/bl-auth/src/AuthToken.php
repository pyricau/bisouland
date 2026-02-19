<?php

declare(strict_types=1);

namespace Bl\Auth;

use Bl\Auth\Account\AccountId;
use Bl\Auth\AuthToken\AuthTokenId;
use Bl\Auth\AuthToken\ExpiresAt;
use Bl\Auth\AuthToken\TokenHash;

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
