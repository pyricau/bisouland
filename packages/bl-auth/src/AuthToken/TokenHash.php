<?php

declare(strict_types=1);

namespace Bl\Auth\AuthToken;

use Bl\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class TokenHash
{
    private function __construct(
        private string $value,
    ) {
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @throws ValidationFailedException If $value isn't a sha256 (64 hexadecimal characters)
     */
    public static function fromString(string $value): self
    {
        if (1 !== preg_match('/^[0-9a-f]{64}$/i', $value)) {
            throw ValidationFailedException::make(
                "Invalid \"TokenHash\" parameter: it should be a sha256, i.e. 64 hexadecimal characters (`{$value}` given)",
            );
        }

        return new self($value);
    }

    public static function fromTokenPlain(TokenPlain $tokenPlain): self
    {
        return new self(hash('sha256', $tokenPlain->toString()));
    }
}
