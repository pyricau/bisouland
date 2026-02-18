<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures;

use Bl\Auth\Account;
use Bl\Auth\Account\AccountId;
use Bl\Auth\Account\PasswordHash;
use Bl\Auth\Account\Username;
use Bl\Auth\Tests\Fixtures\Account\AccountIdFixture;
use Bl\Auth\Tests\Fixtures\Account\PasswordHashFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;

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
