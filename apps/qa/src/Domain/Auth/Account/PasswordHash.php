<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Auth\Account;

use Bl\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class PasswordHash
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
     * @throws ValidationFailedException If $value isn't a valid password hash
     */
    public static function fromString(string $value): self
    {
        if ('' === $value) {
            throw ValidationFailedException::make(
                'Invalid "PasswordHash" parameter: it cannot be empty',
            );
        }

        $info = password_get_info($value);
        if (null === $info['algo']) {
            throw ValidationFailedException::make(
                'Invalid "PasswordHash" parameter: it is not a recognized password hash',
            );
        }

        return new self($value);
    }

    public static function fromPasswordPlain(PasswordPlain $passwordPlain): self
    {
        return new self(password_hash($passwordPlain->toString(), \PASSWORD_DEFAULT));
    }
}
