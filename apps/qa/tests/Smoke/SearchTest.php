<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class SearchTest extends TestCase
{
    #[TestDox('it loads search page (`/recherche.html`)')]
    public function test_it_loads_search_page(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/recherche.html');

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
