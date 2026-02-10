<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Smoke;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\Tests\Monolith\Smoke\Assertion\Assert;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class PublicPagesTest extends TestCase
{
    #[TestDox('It loads $pageName page (`$url`)')]
    #[DataProvider('publicPagesProvider')]
    public function test_it_loads_public_page(string $url, string $pageName): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', $url);

        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        Assert::noPhpErrorsOrWarnings($response);
    }

    /**
     * @return \Iterator<array{
     *      url: string,
     *      pageName: string,
     *  }>
     */
    public static function publicPagesProvider(): \Iterator
    {
        yield ['url' => '/', 'pageName' => 'homepage'];
        yield ['url' => '/contact.html', 'pageName' => 'contact'];
        yield ['url' => '/faq.html', 'pageName' => 'FAQ'];
        yield ['url' => '/livreor.html', 'pageName' => 'guestbook'];
        yield ['url' => '/aide.html', 'pageName' => 'help'];
        yield ['url' => '/connexion.html', 'pageName' => 'login'];
        yield ['url' => '/membres.html', 'pageName' => 'players'];
        yield ['url' => '/topten.html', 'pageName' => 'ranking'];
        yield ['url' => '/recherche.html', 'pageName' => 'search'];
        yield ['url' => '/inscription.html', 'pageName' => 'signup'];
        yield ['url' => '/stats.html', 'pageName' => 'statistics'];
    }
}
