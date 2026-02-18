<?php

declare(strict_types=1);

namespace Bl\Game\Tests\Fixtures\Player;

use Bl\Game\Player\MilliScore;

final readonly class MilliScoreFixture
{
    public static function make(): MilliScore
    {
        return MilliScore::fromInt(self::makeInt());
    }

    public static function makeInt(): int
    {
        return random_int(0, 10000);
    }
}
