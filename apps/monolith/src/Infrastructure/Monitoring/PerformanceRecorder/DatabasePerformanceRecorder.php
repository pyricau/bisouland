<?php

namespace Bl\Infrastructure\Monitoring\PerformanceRecorder;

use Bl\Infrastructure\Monitoring\PerformanceRecorder;

/**
 * Records performance metrics to the database for monitoring.
 */
class DatabasePerformanceRecorder implements PerformanceRecorder
{
    /**
     * @var \PDO $pdo
     */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function record($operation, $duration)
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO performance_metrics (timestamp, operation, duration) VALUES (?, ?, ?)'
            );
            $stmt->execute(array(time(), $operation, $duration));
        } catch (\Exception $e) {
            // Silently fail - don't break the application if recording fails
        }
    }
}
