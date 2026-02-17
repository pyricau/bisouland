<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Web\Action;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class InstantFreeUpgradeControllerTest extends TestCase
{
    public function test_it_renders_the_instant_free_upgrade_page(): void
    {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/actions/instant-free-upgrade',
            method: 'GET',
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
    }
}
