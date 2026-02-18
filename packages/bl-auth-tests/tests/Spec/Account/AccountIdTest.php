<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Spec\Account;

use Bl\Auth\Account\AccountId;
use Bl\Auth\Tests\Fixtures\Account\AccountIdFixture;
use Bl\Exception\ValidationFailedException;
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

    #[TestDox('It generates unique IDs')]
    public function test_it_generates_unique_ids(): void
    {
        $accountId1 = AccountId::create();
        $accountId2 = AccountId::create();

        // If fromString doesn't throw, the format is valid
        AccountId::fromString($accountId1->toString());
        AccountId::fromString($accountId2->toString());

        $this->assertInstanceOf(AccountId::class, $accountId1);
        $this->assertInstanceOf(AccountId::class, $accountId2);
        $this->assertNotSame($accountId1->toString(), $accountId2->toString());
    }

    #[TestDox('It fails when it is not a valid UUID')]
    public function test_it_fails_when_it_is_not_a_valid_uuid(): void
    {
        $this->expectException(ValidationFailedException::class);

        AccountId::fromString('not-a-valid-uuid');
    }
}
