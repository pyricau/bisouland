<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure;

use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Forces `capture_stderr_separately` on every run(), so that `getErrorOutput()`
 * is always available in assertions when a test fails.
 * Without it, stderr and stdout are merged and cannot be distinguished.
 */
final class StderrApplicationTester extends ApplicationTester
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $options
     */
    public function run(array $input, array $options = []): int
    {
        return parent::run($input, ['capture_stderr_separately' => true, ...$options]);
    }
}
