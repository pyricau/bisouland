<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Auth\Account;

use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class Username
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
     * @throws ValidationFailedException If $value isn't valid
     */
    public static function fromString(string $value): self
    {
        $length = \strlen($value);
        if ($length < 4 || $length > 15) {
            throw ValidationFailedException::make(
                "Invalid \"Username\" parameter: it should be between 4 and 15 characters (`{$value}`, {$length} characters given)",
            );
        }

        if (1 !== preg_match('/^\w+$/', $value)) {
            throw ValidationFailedException::make(
                "Invalid \"Username\" parameter: it should only contain alphanumerical characters and underscores (`{$value}` given)",
            );
        }

        return new self($value);
    }
}
