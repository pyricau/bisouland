<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\Account;

use Bl\Domain\Auth\Account\AccountId;
use Bl\Domain\Exception\ValidationFailedException;
use Bl\Tests\Spec\Domain\Auth\Account\Fixtures\AccountIdFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountId::class)]
#[Small]
final class AccountIdTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringAccountId = AccountIdFixture::makeString();
        $accountId = AccountId::fromString($stringAccountId);

        $this->assertInstanceOf(AccountId::class, $accountId);
        $this->assertSame($stringAccountId, $accountId->toString());
    }

    #[TestDox('It fails when it is not a valid UUID')]
    public function test_it_fails_when_it_is_not_a_valid_uuid(): void
    {
        $this->expectException(ValidationFailedException::class);

        AccountId::fromString('not-a-valid-uuid');
    }
}
