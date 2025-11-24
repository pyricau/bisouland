<?php

namespace Bl\Infrastructure\Pg;

class CastToPgTimestamptz
{
    /**
     * PostgreSQL's TIMESTAMPTZ fields are strings in (sort of) ISO 8601 date format:
     * - '2025-11-20T16:45:03.336548+00:00' (fully ISO 8601 compliant)
     * - '2025-11-20 16:45:03+00'
     * - '2025-11-20 16:45:03+00:00'
     * - '2025-11-20 16:45:03.336548+00'
     * - '2025-11-20 16:45:03.336548+00:00'
     */
    public function fromUnixTimestamp(int $unixTimestamp): string
    {
        return new \DateTimeImmutable("@{$unixTimestamp}")->format('Y-m-d\TH:i:s.uP');
    }
}
