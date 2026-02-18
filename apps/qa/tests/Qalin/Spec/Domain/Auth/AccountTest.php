<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Auth;

use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\AccountIdFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordHashFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\AccountFixture;
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
