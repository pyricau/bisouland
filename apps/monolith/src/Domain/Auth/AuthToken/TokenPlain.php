<?php

declare(strict_types=1);

namespace Bl\Domain\Auth\AuthToken;

use Bl\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class TokenPlain
{
    private function __construct(
        #[\SensitiveParameter]
        private string $value,
    ) {
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @throws ValidationFailedException If $value isn't 32 hexadecimal characters
     */
    public static function fromString(
        #[\SensitiveParameter]
        string $value,
    ): self {
        if (1 !== preg_match('/^[0-9a-f]{32}$/i', $value)) {
            // Invalid value aren't technically tokens, so aren't sensitive
            throw ValidationFailedException::make(
                "Invalid \"TokenPlain\" parameter: it should be 32 hexadecimal characters (`{$value}` given)",
            );
        }

        return new self($value);
    }

    public static function create(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }
}
