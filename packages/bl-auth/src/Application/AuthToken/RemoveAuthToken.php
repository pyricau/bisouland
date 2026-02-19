<?php

declare(strict_types=1);

namespace Bl\Auth\Application\AuthToken;

use Bl\Auth\Account\AccountId;
use Bl\Exception\ValidationFailedException;

/**
 * @object-type Command
 */
final readonly class RemoveAuthToken
{
    public function __construct(
        public AccountId $accountId,
    ) {
    }

    /**
     * @throws ValidationFailedException If $rawAccountId isn't a valid UUID
     */
    public static function fromRawAccountId(mixed $rawAccountId): self
    {
        if (false === \is_string($rawAccountId)) {
            $type = get_debug_type($rawAccountId);
            throw ValidationFailedException::make(
                "Invalid \"AccountId\" parameter: it should be a string (`{$type}` given)",
            );
        }

        return new self(
            AccountId::fromString($rawAccountId),
        );
    }
}
