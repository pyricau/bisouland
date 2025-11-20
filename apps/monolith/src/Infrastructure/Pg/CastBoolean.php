<?php

namespace Bl\Infrastructure\Pg;

class CastBoolean
{
    /**
     * PostgreSQL has BOOLEAN column types,
     * but requires the value to be a string literal ('true' or 'false'),
     * so we need this to convert PHP boolean values.
     */
    public function from(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
