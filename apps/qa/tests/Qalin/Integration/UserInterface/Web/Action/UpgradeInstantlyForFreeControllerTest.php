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
final class UpgradeInstantlyForFreeControllerTest extends TestCase
{
    public function test_it_renders_the_upgrade_instantly_for_free_page(): void
    {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/actions/upgrade-instantly-for-free',
            method: 'GET',
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
    }
}
