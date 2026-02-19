<?php

declare(strict_types=1);

namespace Bl\Game\Tests\Fixtures\Player;

use Bl\Game\Player\UpgradableLevels;

final readonly class UpgradableLevelsFixture
{
    public static function make(
        ?int $heart = null,
        ?int $mouth = null,
        ?int $tongue = null,
        ?int $teeth = null,
        ?int $legs = null,
        ?int $eyes = null,
        ?int $peck = null,
        ?int $smooch = null,
        ?int $frenchKiss = null,
        ?int $holdBreath = null,
        ?int $flirt = null,
        ?int $spit = null,
        ?int $leap = null,
        ?int $soup = null,
    ): UpgradableLevels {
        return UpgradableLevels::fromInts(
            heart: $heart ?? UpgradableLevels::STARTING_LEVELS['heart'],
            mouth: $mouth ?? UpgradableLevels::STARTING_LEVELS['mouth'],
            tongue: $tongue ?? UpgradableLevels::STARTING_LEVELS['tongue'],
            teeth: $teeth ?? UpgradableLevels::STARTING_LEVELS['teeth'],
            legs: $legs ?? UpgradableLevels::STARTING_LEVELS['legs'],
            eyes: $eyes ?? UpgradableLevels::STARTING_LEVELS['eyes'],
            peck: $peck ?? UpgradableLevels::STARTING_LEVELS['peck'],
            smooch: $smooch ?? UpgradableLevels::STARTING_LEVELS['smooch'],
            frenchKiss: $frenchKiss ?? UpgradableLevels::STARTING_LEVELS['french_kiss'],
            holdBreath: $holdBreath ?? UpgradableLevels::STARTING_LEVELS['hold_breath'],
            flirt: $flirt ?? UpgradableLevels::STARTING_LEVELS['flirt'],
            spit: $spit ?? UpgradableLevels::STARTING_LEVELS['spit'],
            leap: $leap ?? UpgradableLevels::STARTING_LEVELS['leap'],
            soup: $soup ?? UpgradableLevels::STARTING_LEVELS['soup'],
        );
    }
}
