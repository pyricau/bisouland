<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game\Player;

use Bl\Qa\Domain\Game\Player\MilliScore;

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
