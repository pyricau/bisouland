<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[Large]
final class HomepageTest extends TestCase
{
    public function test_it_loads_homepage(): void
    {
        // $appKernel = AppSingleton::get()->appKernel();

        // $request = Request::create('/', 'GET');

        // $response = $appKernel->handle($request);

        // $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
