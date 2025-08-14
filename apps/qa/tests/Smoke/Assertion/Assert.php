<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke\Assertion;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class Assert
{
    private const string NOT_LOGGED_IN = 'pas connecté !!';

    public static function blocksPageForLoggedOutVisitors(ResponseInterface $response): void
    {
        $content = (string) $response->getContent();
        if (false === str_contains($content, self::NOT_LOGGED_IN)) {
            PHPUnitAssert::fail('Failed asserting that Page is blocked for logged out visitors');
        }

        PHPUnitAssert::assertSame(200, $response->getStatusCode(), $content);
    }

    public static function loadsPageForLoggedInPlayers(ResponseInterface $response): void
    {
        $content = (string) $response->getContent();
        if (true === str_contains($content, self::NOT_LOGGED_IN)) {
            PHPUnitAssert::fail('Failed asserting that Page loads for logged in players');
        }

        PHPUnitAssert::assertSame(200, $response->getStatusCode(), $content);
    }
}
