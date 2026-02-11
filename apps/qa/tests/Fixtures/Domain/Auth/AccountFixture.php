<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Auth;

use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Domain\Auth\Account\AccountId;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\AccountIdFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordHashFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;

final readonly class AccountFixture
{
    public static function make(
        ?AccountId $accountId = null,
        ?Username $username = null,
        ?PasswordHash $passwordHash = null,
    ): Account {
        return new Account(
            accountId: $accountId ?? AccountIdFixture::make(),
            username: $username ?? UsernameFixture::make(),
            passwordHash: $passwordHash ?? PasswordHashFixture::make(),
        );
    }
}
