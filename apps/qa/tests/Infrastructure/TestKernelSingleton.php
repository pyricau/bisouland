<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure;

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
