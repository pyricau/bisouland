<?php

declare(strict_types=1);

namespace Bl\Domain\Auth\AuthToken;

use Bl\Domain\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class ExpiresAt
{
    /** @var array<string> \DateTimeInterface string format */
    private const array ACCEPTED_ISO_8601S = [
        'Y-m-d\\TH:i:s', // 2025-06-17T13:00:00
        'Y-m-d\\TH:i:s\\Z', // 2025-06-17T13:00:00Z
        'Y-m-d\\TH:i:sP', // 2025-06-17T13:00:00+00:00 / 2025-06-17T13:00:00-05:00
        'Y-m-d\\TH:i:s.u', // 2025-06-17T13:00:00.123456
        'Y-m-d\\TH:i:s.u\\Z', // 2025-06-17T13:00:00.123456Z
        'Y-m-d\\TH:i:s.uP', // 2025-06-17T13:00:00.123456+02:00
    ];

    public const string ISO_8601 = 'Y-m-d\\TH:i:s.uP';

    public const string DEFAULT_DURATION = '+15 days';

    private function __construct(
        private \DateTimeImmutable $value,
    ) {
    }

    public function toString(string $format = self::ISO_8601): string
    {
        return $this->value->format($format);
    }

    /**
     * @param string $value A valid ISO 8601 date (e.g. `2025-06-17T13:00:00`)
     *
     * @throws ValidationFailedException If $value isn't a valid ISO 8601 date
     *
     * @see self::ACCEPTED_ISO_8601S (private class constant)
     */
    public static function fromString(string $value): self
    {
        foreach (self::ACCEPTED_ISO_8601S as $iso8601) {
            $dateTime = \DateTimeImmutable::createFromFormat($iso8601, $value);
            if (
                false !== $dateTime
                && $value === $dateTime->format($iso8601)
            ) {
                return new self($dateTime);
            }
        }

        throw ValidationFailedException::make("Invalid \"ExpiresAt\" parameter: it should be a valid ISO 8601 date (`{$value}` given)");
    }

    /**
     * Can be used to create ExpiresAt from cookie `expires`.
     *
     * @param int $value A valid UNIX timestamp (e.g. `1766128779`)
     *
     * @throws ValidationFailedException If $value isn't a valid UNIX timestamp
     */
    public static function fromTimestamp(int $value): self
    {
        $dateTime = \DateTimeImmutable::createFromFormat('U', (string) $value);
        if (false !== $dateTime) {
            return new self($dateTime);
        }

        throw ValidationFailedException::make("Invalid \"ExpiresAt\" parameter: it should be a valid UNIX timestamp (`{$value}` given)");
    }

    /**
     * Can be used to set cookie `expires`.
     */
    public function toTimestamp(): int
    {
        return $this->value->getTimestamp();
    }

    /**
     * Can be used to create ExpiresAt from `expires_at` database field
     * (PDO returns `\DateTimeInterface` for PostgreSQL `TIMESTAMPTZ` fields).
     */
    public static function fromDateTime(\DateTimeInterface $value): self
    {
        if ($value instanceof \DateTime) {
            $value = \DateTimeImmutable::createFromMutable($value);
        }

        return new self($value);
    }

    public static function create(): self
    {
        return new self(new \DateTimeImmutable(self::DEFAULT_DURATION));
    }
}
