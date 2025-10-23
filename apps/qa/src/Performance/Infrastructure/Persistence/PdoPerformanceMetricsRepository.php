<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Infrastructure\Persistence;

use Bl\Qa\Performance\Domain\Model\BenchmarkRun;
use Bl\Qa\Performance\Domain\Model\PageTrendPoint;
use Bl\Qa\Performance\Domain\Model\PerformanceSummary;
use Bl\Qa\Performance\Domain\Port\PerformanceMetricsRepository;

final class PdoPerformanceMetricsRepository implements PerformanceMetricsRepository
{
    private const int SECONDS_IN_HOUR = 3600;
    private const int SECONDS_PER_DAY = 86400;
    private const int WINDOW_SECONDS = 300; // 5-minute windows
    private const int MIN_SAMPLES = 10;

    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    /**
     * @return array<string, PerformanceSummary>
     */
    public function getSummary(int $lastHours): array
    {
        $since = time() - ($lastHours * self::SECONDS_IN_HOUR);

        return $this->getSummaryBetween($since, time());
    }

    /**
     * @return array<string, PerformanceSummary>
     */
    public function getSummaryBetween(int $startTimestamp, int $endTimestamp): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                operation,
                COUNT(*) as samples,
                AVG(duration) as avg_ms,
                MIN(duration) as min_ms,
                MAX(duration) as max_ms
            FROM performance_metrics
            WHERE timestamp >= ? AND timestamp <= ?
            GROUP BY operation
            ORDER BY avg_ms DESC
        ');
        $stmt->execute([$startTimestamp, $endTimestamp]);
        $summary = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!\is_array($row) || !\array_key_exists('operation', $row) || !\array_key_exists('samples', $row) || !\array_key_exists('avg_ms', $row) || !\array_key_exists('min_ms', $row) || !\array_key_exists('max_ms', $row)) {
                continue;
            }
            if (!\is_string($row['operation']) || !is_numeric($row['samples']) || !is_numeric($row['avg_ms']) || !is_numeric($row['min_ms']) || !is_numeric($row['max_ms'])) {
                continue;
            }
            $operation = $row['operation'];
            $percentiles = $this->getPercentilesBetween($operation, $startTimestamp, $endTimestamp);
            $summary[$operation] = new PerformanceSummary(
                page: $operation,
                samples: (int) $row['samples'],
                avgMs: round((float) $row['avg_ms'], 2),
                medianMs: $percentiles['p50'],
                p95Ms: $percentiles['p95'],
                p99Ms: $percentiles['p99'],
                minMs: round((float) $row['min_ms'], 2),
                maxMs: round((float) $row['max_ms'], 2),
            );
        }

        return $summary;
    }

    /**
     * @return array{p50: float, p95: float, p99: float}
     */
    private function getPercentilesBetween(string $operation, int $startTimestamp, int $endTimestamp): array
    {
        $stmt = $this->pdo->prepare('
            SELECT duration
            FROM performance_metrics
            WHERE operation = ? AND timestamp >= ? AND timestamp <= ?
            ORDER BY duration
        ');
        $stmt->execute([$operation, $startTimestamp, $endTimestamp]);
        $values = [];
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            if (\is_array($row) && \array_key_exists(0, $row) && is_numeric($row[0])) {
                $values[] = (float) $row[0];
            }
        }
        if ([] === $values) {
            return ['p50' => 0.0, 'p95' => 0.0, 'p99' => 0.0];
        }
        $count = \count($values);

        return [
            'p50' => round($this->percentile($values, 50, $count), 2),
            'p95' => round($this->percentile($values, 95, $count), 2),
            'p99' => round($this->percentile($values, 99, $count), 2),
        ];
    }

    /**
     * @param array<float> $sortedValues
     */
    private function percentile(array $sortedValues, int $percentile, int $count): float
    {
        $index = (int) ceil(($percentile / 100) * $count) - 1;
        $index = max(0, min($index, $count - 1));

        return $sortedValues[$index];
    }

    /**
     * @return array<PageTrendPoint>
     */
    public function getPageTrend(string $page, int $lastHours): array
    {
        $since = time() - ($lastHours * self::SECONDS_IN_HOUR);

        $stmt = $this->pdo->prepare('
            SELECT
                FROM_UNIXTIME(timestamp, "%Y-%m-%d %H:00") as hour,
                AVG(total_time_ms) as avg_ms,
                COUNT(*) as samples
            FROM performance_metrics
            WHERE page = ? AND timestamp >= ?
            GROUP BY hour
            ORDER BY hour ASC
        ');
        $stmt->execute([$page, $since]);

        $trend = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!\is_array($row) || !\array_key_exists('hour', $row) || !\array_key_exists('avg_ms', $row) || !\array_key_exists('samples', $row)) {
                continue;
            }
            if (!\is_string($row['hour']) || !is_numeric($row['avg_ms']) || !is_numeric($row['samples'])) {
                continue;
            }
            $trend[] = new PageTrendPoint(
                hour: $row['hour'],
                avgMs: round((float) $row['avg_ms'], 2),
                samples: (int) $row['samples'],
            );
        }

        return $trend;
    }

    /**
     * @return array<BenchmarkRun>
     */
    public function listBenchmarkRuns(int $lastDays): array
    {
        $since = time() - ($lastDays * self::SECONDS_PER_DAY);

        // Group metrics by 5-minute windows to detect benchmark runs
        $stmt = $this->pdo->prepare('
            SELECT
                FROM_UNIXTIME(MIN(timestamp), "%Y-%m-%d %H:%i") as run_time,
                COUNT(*) as samples,
                MIN(timestamp) as start_ts,
                MAX(timestamp) as end_ts
            FROM performance_metrics
            WHERE timestamp >= ?
            GROUP BY FLOOR(timestamp / ?)
            HAVING samples > ?
            ORDER BY start_ts DESC
        ');
        $stmt->execute([$since, self::WINDOW_SECONDS, self::MIN_SAMPLES]);

        $runs = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!\is_array($row)
                || !\array_key_exists('run_time', $row)
                || !\array_key_exists('samples', $row)
                || !\array_key_exists('start_ts', $row)
                || !\array_key_exists('end_ts', $row)
            ) {
                continue;
            }

            $runTime = $row['run_time'];
            if (!\is_string($runTime)) {
                continue;
            }

            $samples = $row['samples'];
            if (!is_numeric($samples)) {
                continue;
            }

            $startTs = $row['start_ts'];
            if (!is_numeric($startTs)) {
                continue;
            }

            $endTs = $row['end_ts'];
            if (!is_numeric($endTs)) {
                continue;
            }

            $runs[] = new BenchmarkRun(
                runTime: $runTime,
                samples: (int) $samples,
                startTimestamp: (int) $startTs,
                endTimestamp: (int) $endTs,
            );
        }

        return $runs;
    }

    public function pruneOlderThan(int $days): int
    {
        $cutoff = time() - ($days * self::SECONDS_PER_DAY);

        $stmt = $this->pdo->prepare('DELETE FROM performance_metrics WHERE timestamp < ?');
        $stmt->execute([$cutoff]);

        return $stmt->rowCount();
    }
}
