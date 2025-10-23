<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Port;

use Bl\Qa\Performance\Domain\Model\BenchmarkRun;
use Bl\Qa\Performance\Domain\Model\PageTrendPoint;
use Bl\Qa\Performance\Domain\Model\PerformanceSummary;

interface PerformanceMetricsRepository
{
    /**
     * @return array<string, PerformanceSummary>
     */
    public function getSummary(int $lastHours): array;

    /**
     * @return array<string, PerformanceSummary>
     */
    public function getSummaryBetween(int $startTimestamp, int $endTimestamp): array;

    /**
     * @return array<PageTrendPoint>
     */
    public function getPageTrend(string $page, int $lastHours): array;

    /**
     * @return array<BenchmarkRun>
     */
    public function listBenchmarkRuns(int $lastDays): array;

    public function pruneOlderThan(int $days): int;
}
