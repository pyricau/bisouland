<?php

declare(strict_types=1);

namespace Bl\Application\Auth\AuthToken;

use Bl\Domain\Auth\Account\AccountId;
use Bl\Domain\Auth\AuthToken;
use Bl\Domain\Auth\AuthToken\AuthTokenId;
use Bl\Domain\Auth\AuthToken\ExpiresAt;
use Bl\Domain\Auth\AuthToken\TokenHash;
use Bl\Domain\Auth\AuthToken\TokenPlain;
use Bl\Domain\Exception\ValidationFailedException;

/**
 * @object-type Command
 */
final readonly class CreateAuthToken
{
    public function __construct(
        public AuthToken $authToken,
        public TokenPlain $tokenPlain,
    ) {
    }

    /**
     * @throws ValidationFailedException If $rawAccountId isn't a valid UUID
     */
    public static function fromRawAccountId(mixed $rawAccountId): self
    {
        if (false === \is_string($rawAccountId)) {
            $type = get_debug_type($rawAccountId);
            throw ValidationFailedException::make("Invalid \"AccountId\" parameter: it should be a string (`{$type}` given)");
        }

        $tokenPlain = TokenPlain::create();
        $authToken = new AuthToken(
            AuthTokenId::create(),
            TokenHash::fromTokenPlain($tokenPlain),
            AccountId::fromString($rawAccountId),
            ExpiresAt::create(),
        );

        return new self(
            $authToken,
            $tokenPlain,
        );
    }
}
