<?php

namespace Bl\Infrastructure\Monitoring\PerformanceRecorder;

use Bl\Infrastructure\Monitoring\PerformanceRecorder;

/**
 * Null Object implementation - does nothing.
 */
class NullPerformanceRecorder implements PerformanceRecorder
{
    /**
     * {@inheritdoc}
     */
    public function record($operation, $duration)
    {
        // Do nothing
    }
}
