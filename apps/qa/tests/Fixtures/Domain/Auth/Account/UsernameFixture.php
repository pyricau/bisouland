<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Auth\Account;

use Bl\Qa\Domain\Auth\Account\Username;

final readonly class UsernameFixture
{
    public static function make(): Username
    {
        return Username::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return 'BisouTest'.substr(uniqid(), -6);
    }
}
