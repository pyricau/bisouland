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
final class HelpTest extends TestCase
{
    #[TestDox('it loads help page (`/aide.html`)')]
    public function test_it_loads_help_page(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/aide.html');

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
