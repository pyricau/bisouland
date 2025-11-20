<?php

use Bl\Infrastructure\Pg\CastBoolean;

/**
 * Returns a singleton CastBoolean instance for converting values to PostgreSQL boolean format.
 */
function pg_cast_boolean(): CastBoolean
{
    static $castBoolean = null;

    if (null === $castBoolean) {
        $castBoolean = new CastBoolean();
    }

    return $castBoolean;
}
