<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures\Account;

use Bl\Auth\Account\AccountId;
use Symfony\Component\Uid\Uuid;

final readonly class AccountIdFixture
{
    public static function make(): AccountId
    {
        return AccountId::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return Uuid::v7()->toString();
    }
}
