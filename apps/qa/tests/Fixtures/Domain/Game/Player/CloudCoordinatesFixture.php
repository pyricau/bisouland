<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game\Player;

use Bl\Qa\Domain\Game\Player\CloudCoordinates;

final readonly class CloudCoordinatesFixture
{
    public static function make(): CloudCoordinates
    {
        return CloudCoordinates::fromInts(self::makeX(), self::makeY());
    }

    public static function makeX(): int
    {
        return random_int(1, 100);
    }

    public static function makeY(): int
    {
        return random_int(1, 16);
    }
}
