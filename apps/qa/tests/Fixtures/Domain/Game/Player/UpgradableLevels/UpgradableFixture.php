<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevels;

use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;

final readonly class UpgradableFixture
{
    public static function make(): Upgradable
    {
        return Upgradable::Heart;
    }

    public static function makeString(): string
    {
        return 'heart';
    }
}
