<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type ValueObject
 *
 * X is the cloud number (1-grows as players join)
 * Y is the fluffy spot (1-16) on that cloud.
 */
final readonly class CloudCoordinates
{
    /**
     * @param int $x The cloud number (1-grows as players join)
     * @param int $y The fluffy spot on the cloud (1-16)
     */
    private function __construct(
        private int $x,
        private int $y,
    ) {
    }

    /**
     * @return int The cloud number (1-grows as players join)
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int The fluffy spot on the cloud (1-16)
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @throws ValidationFailedException If $x is less than 1
     * @throws ValidationFailedException If $y is less than 1
     * @throws ValidationFailedException If $y is more than 16
     */
    public static function fromInts(int $x, int $y): self
    {
        if ($x < 1) {
            throw ValidationFailedException::make(
                "Invalid \"CloudCoordinates\" parameter: x should be >= 1 (`{$x}` given)",
            );
        }

        if ($y < 1) {
            throw ValidationFailedException::make(
                "Invalid \"CloudCoordinates\" parameter: y should be >= 1 (`{$y}` given)",
            );
        }

        if ($y > 16) {
            throw ValidationFailedException::make(
                "Invalid \"CloudCoordinates\" parameter: y should be <= 16 (`{$y}` given)",
            );
        }

        return new self($x, $y);
    }

    public static function create(): self
    {
        return new self(1, 1);
    }
}
