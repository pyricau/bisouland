<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Application\UseCase;

use Bl\Qa\Performance\Domain\Model\BenchmarkRun;
use Bl\Qa\Performance\Domain\Port\PerformanceMetricsRepository;

final class ListBenchmarkRuns
{
    public function __construct(
        private readonly PerformanceMetricsRepository $repository,
    ) {
    }

    /**
     * @return array<BenchmarkRun>
     */
    public function forLastDays(int $days): array
    {
        return $this->repository->listBenchmarkRuns($days);
    }
}
