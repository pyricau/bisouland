<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Auth\Account;

use Bl\Qa\Domain\Auth\Account\PasswordHash;

final readonly class PasswordHashFixture
{
    public static function make(): PasswordHash
    {
        return PasswordHash::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return password_hash(PasswordPlainFixture::makeString(), \PASSWORD_DEFAULT);
    }
}
