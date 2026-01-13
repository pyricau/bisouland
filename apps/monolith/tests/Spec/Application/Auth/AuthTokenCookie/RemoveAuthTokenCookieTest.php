<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Application\Auth\AuthTokenCookie;

use Bl\Application\Auth\AuthTokenCookie\RemoveAuthTokenCookie;
use Bl\Domain\Auth\AuthTokenCookie\Credentials;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(RemoveAuthTokenCookie::class)]
#[Small]
final class RemoveAuthTokenCookieTest extends TestCase
{
    public function test_it_provides_cookie_name(): void
    {
        $removeAuthTokenCookie = new RemoveAuthTokenCookie();

        $this->assertSame(Credentials::NAME, $removeAuthTokenCookie->getName());
    }

    public function test_it_provides_empty_cookie_value(): void
    {
        $removeAuthTokenCookie = new RemoveAuthTokenCookie();

        $this->assertSame('', $removeAuthTokenCookie->getValue());
    }

    public function test_it_provides_safe_cookie_options_with_past_expiration(): void
    {
        $removeAuthTokenCookie = new RemoveAuthTokenCookie();

        $this->assertSame([
            'expires' => 1,
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict',
            'path' => '/',
        ], $removeAuthTokenCookie->getOptions());
    }
}
