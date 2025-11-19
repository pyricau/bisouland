<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke\Assertion;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class Assert
{
    private const array NOT_LOGGED_IN_MESSAGES = [
        // Warning: side bar contains `Tu n'es pas connect&eacute;.`
        'standard' => 'es pas connecté !!',
        'variant 1 (inbox)' => 'es pas connect&eacute; !!',
        'variant 2 (kisses, organs, techniques, account)' => 'Veuillez vous connecter.',
        'variant 3 (reference)' => 'Erreur... et vouaip !! :D',
    ];

    public static function blocksPageForLoggedOutVisitors(ResponseInterface $response): void
    {
        $content = $response->getContent();

        foreach (self::NOT_LOGGED_IN_MESSAGES as $message) {
            if (str_contains($content, $message)) {
                PHPUnitAssert::assertSame(200, $response->getStatusCode(), $content);

                return;
            }
        }

        PHPUnitAssert::fail('Failed asserting that Page is blocked for logged out visitors');
    }

    public static function loadsPageForLoggedInPlayers(ResponseInterface $response): void
    {
        $content = $response->getContent();

        foreach (self::NOT_LOGGED_IN_MESSAGES as $message) {
            if (str_contains($content, $message)) {
                PHPUnitAssert::fail('Failed asserting that Page loads for logged in players');
            }
        }

        PHPUnitAssert::assertSame(200, $response->getStatusCode(), $content);
    }

    public static function noPhpErrorsOrWarnings(ResponseInterface $response): void
    {
        $content = $response->getContent();

        $phpErrorPatterns = [
            'An error occurred',
            '<b>Warning</b>',
            '<b>Notice</b>',
            '<b>Fatal error</b>',
            '<b>Parse error</b>',
            '<b>Deprecated</b>',
        ];

        foreach ($phpErrorPatterns as $phpErrorPattern) {
            if (str_contains($content, $phpErrorPattern)) {
                PHPUnitAssert::fail("Failed asserting that response has no PHP errors or warnings. Found: {$phpErrorPattern}");
            }
        }
    }
}
