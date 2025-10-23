<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\Domain\Model;

final readonly class SlowPage
{
    public function __construct(
        public string $page,
        public float $avgMs,
        public float $p95Ms,
        public int $samples,
    ) {
    }
}
