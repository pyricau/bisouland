<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure\Scenario;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;

final readonly class DeleteAllTestPlayers
{
    public static function run(): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $pdo->query("DELETE FROM membres WHERE pseudo LIKE 'BisouTest%'");
    }
}
