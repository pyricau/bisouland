<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Auth\Account;

use Bl\Qa\Domain\Auth\Account\AccountId;
use Symfony\Component\Uid\Uuid;

final readonly class AccountIdFixture
{
    public static function make(): AccountId
    {
        $value = self::makeString();

        return AccountId::fromString($value);
    }

    public static function makeString(): string
    {
        return Uuid::v7()->toString();
    }
}
