<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Model;

final readonly class BenchmarkRun
{
    public function __construct(
        public string $runTime,
        public int $samples,
        public int $startTimestamp,
        public int $endTimestamp,
    ) {
    }

    public function durationSeconds(): int
    {
        return $this->endTimestamp - $this->startTimestamp;
    }
}
