<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game\Player;

use Bl\Qa\Domain\Game\Player\LovePoints;

final readonly class LovePointsFixture
{
    public static function make(): LovePoints
    {
        return LovePoints::fromInt(self::makeInt());
    }

    public static function makeInt(): int
    {
        return random_int(0, 1000);
    }
}
