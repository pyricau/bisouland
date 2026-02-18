<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure;

use Symfony\Component\Console\Tester\ApplicationTester;

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
