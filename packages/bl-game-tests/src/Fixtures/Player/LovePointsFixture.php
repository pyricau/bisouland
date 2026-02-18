<?php

declare(strict_types=1);

namespace Bl\Game\Tests\Fixtures\Player;

use Bl\Game\Player\LovePoints;

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
