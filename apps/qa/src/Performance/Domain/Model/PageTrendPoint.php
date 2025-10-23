<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Model;

final readonly class PageTrendPoint
{
    public function __construct(
        public string $hour,
        public float $avgMs,
        public int $samples,
    ) {
    }
}
