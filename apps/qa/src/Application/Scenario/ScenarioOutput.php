<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Scenario;

interface ScenarioOutput
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(): array;
}
