<?php

namespace Bl\Infrastructure\Monitoring;

interface PerformanceRecorder
{
    /**
     * Record a performance metric.
     *
     * @param string $operation Operation name (e.g., page name)
     * @param float  $duration  Duration in milliseconds
     *
     * @return void
     */
    public function record($operation, $duration);
}
