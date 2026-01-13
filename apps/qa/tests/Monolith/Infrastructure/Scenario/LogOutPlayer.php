<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure\Scenario;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;

final readonly class LogOutPlayer
{
    public static function run(LoggedInPlayer $loggedInPlayer): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $httpClient->request('GET', '/logout.html', [
            'headers' => [
                'Cookie' => $loggedInPlayer->sessionCookie,
            ],
        ]);
    }
}
