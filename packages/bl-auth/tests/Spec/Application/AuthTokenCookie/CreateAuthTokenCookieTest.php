<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Spec\Application\AuthTokenCookie;

use Bl\Auth\Application\AuthToken\CreateAuthToken;
use Bl\Auth\Application\AuthTokenCookie\CreateAuthTokenCookie;
use Bl\Auth\AuthToken;
use Bl\Auth\AuthToken\TokenHash;
use Bl\Auth\AuthTokenCookie\Credentials;
use Bl\Auth\Tests\Fixtures\Account\AccountIdFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\AuthTokenIdFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\ExpiresAtFixture;
use Bl\Auth\Tests\Fixtures\AuthToken\TokenPlainFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateAuthTokenCookie::class)]
#[Small]
final class CreateAuthTokenCookieTest extends TestCase
{
    #[TestDox('It has Credentials')]
    public function test_it_has_credentials(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();
        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );
        $expiresAt = ExpiresAtFixture::make();

        $createAuthTokenCookie = new CreateAuthTokenCookie(
            $credentials,
            $expiresAt,
        );

        $this->assertSame($credentials, $createAuthTokenCookie->credentials);
    }

    #[TestDox('It has ExpiresAt')]
    public function test_it_has_expires_at(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();
        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );
        $expiresAt = ExpiresAtFixture::make();

        $createAuthTokenCookie = new CreateAuthTokenCookie(
            $credentials,
            $expiresAt,
        );

        $this->assertSame($expiresAt, $createAuthTokenCookie->expiresAt);
    }

    public function test_it_provides_cookie_name(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();
        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );
        $expiresAt = ExpiresAtFixture::make();

        $createAuthTokenCookie = new CreateAuthTokenCookie(
            $credentials,
            $expiresAt,
        );

        $this->assertSame(Credentials::NAME, $createAuthTokenCookie->getName());
    }

    public function test_it_provides_cookie_value(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();
        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );
        $expiresAt = ExpiresAtFixture::make();

        $createAuthTokenCookie = new CreateAuthTokenCookie(
            $credentials,
            $expiresAt,
        );

        $this->assertSame($credentials->toCookie(), $createAuthTokenCookie->getValue());
    }

    public function test_it_provides_safe_cookie_options(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();
        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );
        $expiresAt = ExpiresAtFixture::make();

        $createAuthTokenCookie = new CreateAuthTokenCookie(
            $credentials,
            $expiresAt,
        );

        $this->assertSame([
            'expires' => $expiresAt->toTimestamp(),
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict',
            'path' => '/',
        ], $createAuthTokenCookie->getOptions());
    }

    #[TestDox('It can be created from CreateAuthToken')]
    public function test_it_can_be_created_from_create_auth_token(): void
    {
        $tokenPlain = TokenPlainFixture::make();
        $authTokenId = AuthTokenIdFixture::make();
        $expiresAt = ExpiresAtFixture::make();
        $authToken = new AuthToken(
            $authTokenId,
            TokenHash::fromTokenPlain($tokenPlain),
            AccountIdFixture::make(),
            $expiresAt,
        );
        $createAuthToken = new CreateAuthToken($authToken, $tokenPlain);

        $createAuthTokenCookie = CreateAuthTokenCookie::fromCreateAuthToken($createAuthToken);

        $this->assertInstanceOf(CreateAuthTokenCookie::class, $createAuthTokenCookie);
        $this->assertSame($authTokenId, $createAuthTokenCookie->credentials->authTokenId);
        $this->assertSame($tokenPlain, $createAuthTokenCookie->credentials->tokenPlain);
        $this->assertSame($expiresAt, $createAuthTokenCookie->expiresAt);
    }
}
