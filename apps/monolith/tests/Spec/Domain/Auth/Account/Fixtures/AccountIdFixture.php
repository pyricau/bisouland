<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\Account\Fixtures;

use Bl\Domain\Auth\Account\AccountId;
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
