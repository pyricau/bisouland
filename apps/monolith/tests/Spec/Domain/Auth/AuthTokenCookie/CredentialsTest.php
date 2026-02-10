<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthTokenCookie;

use Bl\Domain\Auth\AuthTokenCookie\Credentials;
use Bl\Domain\Exception\ValidationFailedException;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\AuthTokenIdFixture;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\TokenPlainFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Credentials::class)]
#[Small]
final class CredentialsTest extends TestCase
{
    public function test_it_provides_the_cookie_name(): void
    {
        $this->assertSame('bl_auth_token', Credentials::NAME);
    }

    #[TestDox('It has AuthTokenId')]
    public function test_it_has_auth_token_id(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();

        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );

        $this->assertSame($authTokenId, $credentials->authTokenId);
    }

    #[TestDox('It has TokenPlain')]
    public function test_it_has_token_plain(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();

        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );

        $this->assertSame($tokenPlain, $credentials->tokenPlain);
    }

    #[TestDox('It can be formatted to cookie value (`auth_token_id:token_plain`)')]
    public function test_it_can_be_formatted_to_cookie_value(): void
    {
        $authTokenId = AuthTokenIdFixture::make();
        $tokenPlain = TokenPlainFixture::make();

        $credentials = new Credentials(
            $authTokenId,
            $tokenPlain,
        );

        $this->assertSame(
            "{$authTokenId->toString()}:{$tokenPlain->toString()}",
            $credentials->toCookie(),
        );
    }

    #[TestDox('It can be created from cookie value (`auth_token_id:token_plain`)')]
    public function test_it_can_be_created_from_cookie_value(): void
    {
        $stringAuthTokenId = AuthTokenIdFixture::makeString();
        $stringTokenPlain = TokenPlainFixture::makeString();

        $credentials = Credentials::fromCookie(
            "{$stringAuthTokenId}:{$stringTokenPlain}",
        );

        $this->assertSame($stringAuthTokenId, $credentials->authTokenId->toString());
        $this->assertSame($stringTokenPlain, $credentials->tokenPlain->toString());
    }

    #[TestDox('It fails when cookie value $scenario')]
    #[DataProvider('invalidCookieValueProvider')]
    public function test_it_fails_when_cookie_value_is_invalid(
        string $scenario,
        string $invalidCookieValue,
    ): void {
        $this->expectException(ValidationFailedException::class);

        Credentials::fromCookie($invalidCookieValue);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidCookieValue: string,
     *  }>
     */
    public static function invalidCookieValueProvider(): \Iterator
    {
        $stringAuthTokenId = AuthTokenIdFixture::makeString();
        $stringTokenPlain = TokenPlainFixture::makeString();

        yield ['scenario' => 'is an empty string', 'invalidCookieValue' => ''];
        yield ['scenario' => 'has no colon separator', 'invalidCookieValue' => "{$stringAuthTokenId} {$stringTokenPlain}"];
        yield ['scenario' => 'has more than one colon separator', 'invalidCookieValue' => "{$stringAuthTokenId}:{$stringTokenPlain}:SuperSecretBisou"];
        yield ['scenario' => 'has invalid AuthTokenId', 'invalidCookieValue' => "InvalidAuthTokenId:{$stringTokenPlain}"];
        yield ['scenario' => 'has invalid TokenPlain', 'invalidCookieValue' => "{$stringAuthTokenId}:InvalidTokenPlain"];
    }
}
