<?php

namespace Bl\Infrastructure\Pg;

class CastToPgBoolean
{
    /**
     * PostgreSQL's BOOLEAN fields are strings with for values:
     * - `true`, `t`, `TRUE`
     * - `false`, `f`, `FALSE`
     */
    public function from(bool $value): string
    {
        return $value ? 'TRUE' : 'FALSE';
    }
}
