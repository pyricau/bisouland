<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth;

use Bl\Domain\Auth\AuthToken;
use Bl\Tests\Spec\Domain\Auth\Account\Fixtures\AccountIdFixture;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\AuthTokenIdFixture;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\ExpiresAtFixture;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\TokenHashFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthToken::class)]
#[Small]
final class AuthTokenTest extends TestCase
{
    #[TestDox('It has AuthTokenId')]
    public function test_it_has_auth_token_id(): void
    {
        $accountId = AccountIdFixture::make();
        $authTokenId = AuthTokenIdFixture::make();
        $expiresAt = ExpiresAtFixture::make();
        $tokenHash = TokenHashFixture::make();

        $authToken = new AuthToken(
            $authTokenId,
            $tokenHash,
            $accountId,
            $expiresAt,
        );

        $this->assertSame($authTokenId, $authToken->authTokenId);
    }

    #[TestDox('It has TokenHash')]
    public function test_it_has_token_hash(): void
    {
        $accountId = AccountIdFixture::make();
        $authTokenId = AuthTokenIdFixture::make();
        $expiresAt = ExpiresAtFixture::make();
        $tokenHash = TokenHashFixture::make();

        $authToken = new AuthToken(
            $authTokenId,
            $tokenHash,
            $accountId,
            $expiresAt,
        );

        $this->assertSame($tokenHash, $authToken->tokenHash);
    }

    #[TestDox('It has AccountId')]
    public function test_it_has_account_id(): void
    {
        $accountId = AccountIdFixture::make();
        $authTokenId = AuthTokenIdFixture::make();
        $expiresAt = ExpiresAtFixture::make();
        $tokenHash = TokenHashFixture::make();

        $authToken = new AuthToken(
            $authTokenId,
            $tokenHash,
            $accountId,
            $expiresAt,
        );

        $this->assertSame($accountId, $authToken->accountId);
    }

    #[TestDox('It has ExpiresAt')]
    public function test_it_has_expires_at(): void
    {
        $accountId = AccountIdFixture::make();
        $authTokenId = AuthTokenIdFixture::make();
        $expiresAt = ExpiresAtFixture::make();
        $tokenHash = TokenHashFixture::make();

        $authToken = new AuthToken(
            $authTokenId,
            $tokenHash,
            $accountId,
            $expiresAt,
        );

        $this->assertSame($expiresAt, $authToken->expiresAt);
    }
}
