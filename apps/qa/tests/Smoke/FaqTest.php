<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use Bl\Qa\Tests\Smoke\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class FaqTest extends TestCase
{
    #[TestDox('it loads faq page (`/faq.html`)')]
    public function test_it_loads_faq_page(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/faq.html');

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
