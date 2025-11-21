<?php

namespace Bl\Infrastructure\Pg;

class CastToUnixTimestamp
{
    /**
     * PostgreSQL's TIMESTAMPTZ fields are strings in (sort of) ISO 8601 date format:
     * - '2025-11-20T16:45:03.336548+00:00' (fully ISO 8601 compliant)
     * - '2025-11-20 16:45:03+00'
     * - '2025-11-20 16:45:03+00:00'
     * - '2025-11-20 16:45:03.336548+00'
     * - '2025-11-20 16:45:03.336548+00:00'
     */
    public function fromPgTimestamptz(string $timestamptz): int
    {
        return new \DateTimeImmutable($timestamptz)->getTimestamp();
    }
}
