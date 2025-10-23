<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Application\UseCase;

use Bl\Qa\Performance\Domain\Port\PerformanceMetricsRepository;

final class PruneMetrics
{
    public function __construct(
        private readonly PerformanceMetricsRepository $repository,
    ) {
    }

    public function olderThan(int $days): int
    {
        return $this->repository->pruneOlderThan($days);
    }
}
