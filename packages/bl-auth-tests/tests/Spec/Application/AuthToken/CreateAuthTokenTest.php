<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Spec\Application\AuthToken;

use Bl\Auth\Application\AuthToken\CreateAuthToken;
use Bl\Auth\AuthToken;
use Bl\Auth\AuthToken\TokenHash;
use Bl\Auth\Tests\Fixtures\Account\AccountIdFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\AuthTokenIdFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\ExpiresAtFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\TokenPlainFixture;
use Bl\Exception\ValidationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateAuthToken::class)]
#[Small]
final class CreateAuthTokenTest extends TestCase
{
    #[TestDox('It has AuthToken')]
    public function test_it_has_auth_token(): void
    {
        $tokenPlain = TokenPlainFixture::make();
        $authToken = new AuthToken(
            AuthTokenIdFixture::make(),
            TokenHash::fromTokenPlain($tokenPlain),
            AccountIdFixture::make(),
            ExpiresAtFixture::make(),
        );

        $createAuthToken = new CreateAuthToken(
            $authToken,
            $tokenPlain,
        );

        $this->assertSame($authToken, $createAuthToken->authToken);
    }

    #[TestDox('It has TokenPlain')]
    public function test_it_has_token_plain(): void
    {
        $tokenPlain = TokenPlainFixture::make();
        $authToken = new AuthToken(
            AuthTokenIdFixture::make(),
            TokenHash::fromTokenPlain($tokenPlain),
            AccountIdFixture::make(),
            ExpiresAtFixture::make(),
        );

        $createAuthToken = new CreateAuthToken(
            $authToken,
            $tokenPlain,
        );

        $this->assertSame($tokenPlain, $createAuthToken->tokenPlain);
    }

    #[TestDox('It can be created from a raw AccountId')]
    public function test_it_can_be_created_from_a_raw_account_id(): void
    {
        $rawAccountId = AccountIdFixture::makeString();

        $createAuthToken = CreateAuthToken::fromRawAccountId($rawAccountId);

        $this->assertInstanceOf(CreateAuthToken::class, $createAuthToken);
        $this->assertSame($rawAccountId, $createAuthToken->authToken->accountId->toString());
    }

    #[TestDox('It fails when raw account ID $scenario')]
    #[DataProvider('invalidRawAccountIdProvider')]
    public function test_it_fails_when_raw_account_id_is_invalid(
        string $scenario,
        mixed $invalidRawAccountId,
    ): void {
        $this->expectException(ValidationFailedException::class);

        CreateAuthToken::fromRawAccountId($invalidRawAccountId);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidRawAccountId: mixed,
     *  }>
     */
    public static function invalidRawAccountIdProvider(): \Iterator
    {
        yield ['scenario' => 'is null', 'invalidRawAccountId' => null];
        yield ['scenario' => 'is an integer', 'invalidRawAccountId' => 123];
        yield ['scenario' => 'is a float', 'invalidRawAccountId' => 123.45];
        yield ['scenario' => 'is a boolean', 'invalidRawAccountId' => true];
        yield ['scenario' => 'is an array', 'invalidRawAccountId' => []];
        yield ['scenario' => 'is an object', 'invalidRawAccountId' => new \stdClass()];
    }
}
