<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Application\Auth\AuthToken;

use Bl\Application\Auth\AuthToken\RemoveAuthToken;
use Bl\Domain\Exception\ValidationFailedException;
use Bl\Tests\Spec\Domain\Auth\Account\Fixtures\AccountIdFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(RemoveAuthToken::class)]
#[Small]
final class RemoveAuthTokenTest extends TestCase
{
    #[TestDox('It has AccountId')]
    public function test_it_has_account_id(): void
    {
        $accountId = AccountIdFixture::make();

        $removeAuthToken = new RemoveAuthToken($accountId);

        $this->assertSame($accountId, $removeAuthToken->accountId);
    }

    #[TestDox('It can be created from a raw AccountId')]
    public function test_it_can_be_created_from_a_raw_account_id(): void
    {
        $rawAccountId = AccountIdFixture::makeString();

        $removeAuthToken = RemoveAuthToken::fromRawAccountId($rawAccountId);

        $this->assertInstanceOf(RemoveAuthToken::class, $removeAuthToken);
        $this->assertSame($rawAccountId, $removeAuthToken->accountId->toString());
    }

    #[TestDox('It fails when raw account ID $scenario')]
    #[DataProvider('invalidRawAccountIdProvider')]
    public function test_it_fails_when_raw_account_id_is_not_a_string(
        string $scenario,
        mixed $invalidRawAccountId,
    ): void {
        $this->expectException(ValidationFailedException::class);

        RemoveAuthToken::fromRawAccountId($invalidRawAccountId);
    }

    /**
     * @return \Iterator<(int | string), array{string, mixed}>
     */
    public static function invalidRawAccountIdProvider(): \Iterator
    {
        yield ['is null', null];
        yield ['is an integer', 123];
        yield ['is a float', 123.45];
        yield ['is a boolean', true];
        yield ['is an array', []];
        yield ['is an object', new \stdClass()];
    }
}
