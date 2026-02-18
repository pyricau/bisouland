<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action;

interface ActionOutput
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(): array;
}
