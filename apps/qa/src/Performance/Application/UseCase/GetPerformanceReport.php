<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Application\UseCase;

use Bl\Qa\Performance\Domain\Model\PerformanceSummary;
use Bl\Qa\Performance\Domain\Model\SlowPage;
use Bl\Qa\Performance\Domain\Port\PerformanceMetricsRepository;

final class GetPerformanceReport
{
    public function __construct(
        private readonly PerformanceMetricsRepository $repository,
    ) {
    }

    /**
     * @return array<string, PerformanceSummary>
     */
    public function getSummary(int $lastHours = 24): array
    {
        return $this->repository->getSummary($lastHours);
    }

    /**
     * @return array<SlowPage>
     */
    public function getSlowestPages(int $lastHours = 24, int $limit = 10): array
    {
        $summary = $this->repository->getSummary($lastHours);
        // Sort by p95 descending
        usort($summary, fn ($a, $b) => $b->p95Ms <=> $a->p95Ms);

        return \array_slice(array_map(fn (PerformanceSummary $s) => new SlowPage(
            page: $s->page,
            avgMs: $s->avgMs,
            p95Ms: $s->p95Ms,
            samples: $s->samples,
        ), $summary), 0, $limit);
    }
}
