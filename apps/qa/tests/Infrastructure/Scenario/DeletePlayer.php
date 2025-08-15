<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;

final readonly class DeletePlayer
{
    public static function run(Player $player): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = ?');
        $stmt->execute([$player->username]);
    }
}
