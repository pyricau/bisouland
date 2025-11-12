<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class PublicPagesTest extends TestCase
{
    #[TestDox('it loads $pageName page (`$url`)')]
    #[DataProvider('publicPagesProvider')]
    public function test_it_loads_public_page(string $url, string $pageName): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', $url);

        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @return array<array{string, string}>
     */
    public static function publicPagesProvider(): array
    {
        return [
            ['/', 'homepage'],
            ['/contact.html', 'contact'],
            ['/faq.html', 'FAQ'],
            ['/livreor.html', 'guestbook'],
            ['/aide.html', 'help'],
            ['/connexion.html', 'login'],
            ['/membres.html', 'players'],
            ['/topten.html', 'ranking'],
            ['/recherche.html', 'search'],
            ['/inscription.html', 'signup'],
            ['/stats.html', 'statistics'],
        ];
    }
}
