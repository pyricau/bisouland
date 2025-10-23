<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Model;

final readonly class PerformanceSummary
{
    public function __construct(
        public string $page,
        public int $samples,
        public float $avgMs,
        public float $medianMs,
        public float $p95Ms,
        public float $p99Ms,
        public float $minMs,
        public float $maxMs,
    ) {
    }
}
