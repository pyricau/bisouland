<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Application\UseCase;

use Bl\Qa\Performance\Domain\Model\PerformanceComparison;
use Bl\Qa\Performance\Domain\Port\PerformanceMetricsRepository;

final class CompareMetrics
{
    private const int SECONDS_IN_HOUR = 3600;

    public function __construct(
        private readonly PerformanceMetricsRepository $repository,
    ) {
    }

    /**
     * @return array<string, PerformanceComparison>
     */
    public function beforeAfter(string $datetime, int $hours = 1, float $threshold = 5.0): array
    {
        $timestamp = strtotime($datetime);
        if (false === $timestamp) {
            return [];
        }

        $seconds = $hours * self::SECONDS_IN_HOUR;
        $beforeStart = $timestamp - $seconds;
        $beforeEnd = $timestamp - 1;
        $afterStart = $timestamp;
        $afterEnd = $timestamp + $seconds;

        $beforeSummary = $this->repository->getSummaryBetween($beforeStart, $beforeEnd);
        $afterSummary = $this->repository->getSummaryBetween($afterStart, $afterEnd);

        $comparison = [];
        foreach ($afterSummary as $page => $afterData) {
            if (!\array_key_exists($page, $beforeSummary)) {
                continue;
            }
            $beforeData = $beforeSummary[$page];

            // Skip if before data has zero values (can't calculate percentage)
            if (0.0 === $beforeData->avgMs || 0.0 === $beforeData->p95Ms) {
                continue;
            }

            $avgDiff = $afterData->avgMs - $beforeData->avgMs;
            $avgDiffPercent = ($avgDiff / $beforeData->avgMs) * 100;

            $p95Diff = $afterData->p95Ms - $beforeData->p95Ms;
            $p95DiffPercent = ($p95Diff / $beforeData->p95Ms) * 100;

            if (abs($avgDiffPercent) < $threshold && abs($p95DiffPercent) < $threshold) {
                $status = 'similar';
            } elseif ($avgDiffPercent < 0 && $p95DiffPercent < 0) {
                $status = 'improved';
            } else {
                $status = 'degraded';
            }

            $comparison[$page] = new PerformanceComparison(
                page: $page,
                beforeAvg: $beforeData->avgMs,
                afterAvg: $afterData->avgMs,
                beforeP95: $beforeData->p95Ms,
                afterP95: $afterData->p95Ms,
                avgDiffMs: round($avgDiff, 2),
                avgDiffPercent: round($avgDiffPercent, 2),
                p95DiffMs: round($p95Diff, 2),
                p95DiffPercent: round($p95DiffPercent, 2),
                status: $status,
            );
        }

        return $comparison;
    }
}
