#!/usr/bin/env php
<?php

declare(strict_types=1);

$iterations = isset($argv[1]) ? (int) $argv[1] : 10;

if ($iterations < 1) {
    echo "Error: Iterations must be at least 1\n";
    exit(1);
}

echo "Running {$iterations} iterations of smoke tests to generate performance metrics...\n";

$phpunit = __DIR__.'/../vendor/bin/phpunit';

$failed = 0;
for ($i = 1; $i <= $iterations; ++$i) {
    echo "  Iteration {$i}/{$iterations}... ";

    exec("{$phpunit} --testsuite=smoke --no-output 2>&1", $output, $exitCode);

    if (0 === $exitCode) {
        echo "✓\n";
    } else {
        echo "✗\n";
        ++$failed;
    }
}

echo "\n";
echo "Completed {$iterations} iterations ({$failed} failed)\n";

exit($failed > 0 ? 1 : 0);
