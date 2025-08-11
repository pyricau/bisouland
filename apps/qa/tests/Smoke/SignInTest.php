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
final class SignInTest extends TestCase
{
    #[TestDox('it loads sign in page (`/inscription.html`)')]
    public function test_it_loads_sign_in_page(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/inscription.html');

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
