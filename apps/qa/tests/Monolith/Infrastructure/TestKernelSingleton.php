<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure;

/**
 * Ensures the Symfony kernel is booted only once across the entire test suite.
 * Without this, each test class would boot a fresh kernel, which is expensive
 * (builds the container, loads config, etc.).
 *
 * Preferred over KernelTestCase for three reasons:
 * - single boot for the entire suite (KernelTestCase reboots per test class)
 * - no forced inheritance (KernelTestCase extends TestCase, limiting flexibility)
 * - explicit intent (rather than relying on KernelTestCase's internal caching)
 */
final class TestKernelSingleton
{
    private static ?TestKernel $testKernel = null;

    public static function get(): TestKernel
    {
        if (!self::$testKernel instanceof TestKernel) {
            self::$testKernel = TestKernel::make();
        }

        return self::$testKernel;
    }
}
