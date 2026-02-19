<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures\Account;

use Bl\Auth\Account\PasswordPlain;

final readonly class PasswordPlainFixture
{
    public static function make(): PasswordPlain
    {
        return PasswordPlain::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return 'Pass'.bin2hex(random_bytes(4));
    }
}
