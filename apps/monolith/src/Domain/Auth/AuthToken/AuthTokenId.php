<?php

declare(strict_types=1);

namespace Bl\Domain\Auth\AuthToken;

use Bl\Domain\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @object-type ValueObject
 */
final readonly class AuthTokenId
{
    private function __construct(
        private Uuid $value,
    ) {
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
                "Invalid \"AuthTokenId\" parameter: it should be a valid UUID (`{$value}` given)",
            );
        }

        return new self(Uuid::fromString($value));
    }

    public static function create(): self
    {
        return new self(Uuid::v7());
    }
}
