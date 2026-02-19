<?php

declare(strict_types=1);

namespace Bl\Game\Tests\Fixtures\Player\UpgradableLevels;

use Bl\Game\Player\UpgradableLevels\Upgradable;

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
