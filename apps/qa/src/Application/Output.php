<?php

declare(strict_types=1);

namespace Bl\Qa\Application;

interface Output
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(): array;
}
