<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Spec;

use Bl\Auth\Account;
use Bl\Auth\Tests\Fixtures\Account\AccountIdFixture;
use Bl\Auth\Tests\Fixtures\Account\PasswordHashFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Auth\Tests\Fixtures\AccountFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Account::class)]
#[Small]
final class AccountTest extends TestCase
{
    #[TestDox('It has AccountId')]
    public function test_it_has_account_id(): void
    {
        $accountId = AccountIdFixture::make();
        $account = AccountFixture::make(accountId: $accountId);

        $this->assertSame($accountId, $account->accountId);
    }

    #[TestDox('It has Username')]
    public function test_it_has_username(): void
    {
        $username = UsernameFixture::make();
        $account = AccountFixture::make(username: $username);

        $this->assertSame($username, $account->username);
    }

    #[TestDox('It has PasswordHash')]
    public function test_it_has_password_hash(): void
    {
        $passwordHash = PasswordHashFixture::make();
        $account = AccountFixture::make(passwordHash: $passwordHash);

        $this->assertSame($passwordHash, $account->passwordHash);
    }
}
