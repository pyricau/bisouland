<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Model;

final readonly class PerformanceComparison
{
    public function __construct(
        public string $page,
        public float $beforeAvg,
        public float $afterAvg,
        public float $beforeP95,
        public float $afterP95,
        public float $avgDiffMs,
        public float $avgDiffPercent,
        public float $p95DiffMs,
        public float $p95DiffPercent,
        public string $status,
    ) {
    }
}
