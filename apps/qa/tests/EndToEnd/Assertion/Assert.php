<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd\Assertion;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Assert as PHPUnitAssert;

final readonly class Assert
{
    public static function signedUpCount(string $username, int $expectedCount): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $actualCount = (int) $stmt->fetchColumn();

        PHPUnitAssert::assertSame(
            $expectedCount,
            $actualCount,
            "Failed asserting that Signed Up Count {$actualCount} is {$expectedCount}",
        );
    }
}
