<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Auth\Account;

use Bl\Exception\ValidationFailedException;

/**
 * Password rules follow NIST SP 800-63B Rev 4.
 *
 * @see https://pages.nist.gov/800-63-4/sp800-63b.html
 * @see https://drata.com/blog/nist-password-guidelines
 * @see https://www.enzoic.com/blog/nist-sp-800-63b-rev4/
 * @see https://proton.me/blog/nist-password-guidelines
 *
 * - Minimum 8 characters (with MFA, 15 without)
 * - No maximum length imposed
 * - All printable characters allowed (no composition rules)
 * - SHALL NOT require mixed case, digits, or special characters
 *   (complexity rules lead to weaker, more predictable passwords)
 * - No periodic password change required (unless breach evidence)
 *
 * @object-type ValueObject
 */
final readonly class PasswordPlain
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
     * @throws ValidationFailedException If $value isn't valid
     */
    public static function fromString(
        #[\SensitiveParameter]
        string $value,
    ): self {
        $length = \strlen($value);
        if ($length < 8) {
            throw ValidationFailedException::make(
                "Invalid \"PasswordPlain\" parameter: it should be at least 8 characters (`{$length}` characters given)",
            );
        }

        return new self($value);
    }
}
