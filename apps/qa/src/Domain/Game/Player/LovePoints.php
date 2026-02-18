<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 */
final readonly class LovePoints
{
    public const int STARTING_LOVE_POINTS = 300;

    private function __construct(
        private int $value,
    ) {
    }

    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @throws ValidationFailedException If $value is negative
     */
    public static function fromInt(int $value): self
    {
        if ($value < 0) {
            throw ValidationFailedException::make(
                "Invalid \"LovePoints\" parameter: it should be >= 0 (`{$value}` given)",
            );
        }

        return new self($value);
    }

    public static function create(): self
    {
        return new self(self::STARTING_LOVE_POINTS);
    }
}
