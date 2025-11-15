<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use Bl\Qa\Tests\Smoke\Assertion\Assert;
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
        Assert::noPhpErrorsOrWarnings($response);
    }

    /**
     * @return \Iterator<(int | string), array{string, string}>
     */
    public static function publicPagesProvider(): \Iterator
    {
        yield ['/', 'homepage'];
        yield ['/contact.html', 'contact'];
        yield ['/faq.html', 'FAQ'];
        yield ['/livreor.html', 'guestbook'];
        yield ['/aide.html', 'help'];
        yield ['/connexion.html', 'login'];
        yield ['/membres.html', 'players'];
        yield ['/topten.html', 'ranking'];
        yield ['/recherche.html', 'search'];
        yield ['/inscription.html', 'signup'];
        yield ['/stats.html', 'statistics'];
    }
}
