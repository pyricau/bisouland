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
final class GuestbookTest extends TestCase
{
    #[TestDox('it loads guest book page (`/livreor.html`)')]
    public function test_it_loads_guest_book_page(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/livreor.html');

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
