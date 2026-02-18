<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 *
 * The raw score value stored in the database.
 * Divide by 1000 to get the player-facing score: `floor(milli_score / 1000)`.
 *
 * Named after the SI "milli" prefix (1/1000), as in milliseconds.
 */
final readonly class MilliScore
{
    public const int INITIAL = 0;

    private function __construct(
        private int $value,
    ) {
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function toScore(): int
    {
        return (int) floor($this->value / 1000);
    }

    /**
     * @throws ValidationFailedException If $value is negative
     */
    public static function fromInt(int $value): self
    {
        if ($value < 0) {
            throw ValidationFailedException::make(
                "Invalid \"MilliScore\" parameter: it should be >= 0 (`{$value}` given)",
            );
        }

        return new self($value);
    }

    public static function create(): self
    {
        return new self(self::INITIAL);
    }
}
