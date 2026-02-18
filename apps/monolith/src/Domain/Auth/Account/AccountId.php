<?php

declare(strict_types=1);

namespace Bl\Domain\Auth\Account;

use Bl\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @object-type ValueObject
 */
final readonly class AccountId
{
    private function __construct(
        private Uuid $value,
    ) {
    }

    public static function create(): self
    {
        return new self(Uuid::v7());
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    /**
     * @throws ValidationFailedException If $value isn't a valid UUID
     */
    public static function fromString(string $value): self
    {
        if (false === Uuid::isValid($value)) {
            throw ValidationFailedException::make(
                "Invalid \"AccountId\" parameter: it should be a valid UUID (`{$value}` given)",
            );
        }

        return new self(Uuid::fromString($value));
    }
}
