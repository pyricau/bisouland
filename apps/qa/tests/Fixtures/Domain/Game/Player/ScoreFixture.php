<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game\Player;

use Bl\Qa\Domain\Game\Player\Score;

final readonly class ScoreFixture
{
    public static function make(): Score
    {
        return Score::fromInt(self::makeInt());
    }

    public static function makeInt(): int
    {
        return random_int(0, 10000);
    }
}
