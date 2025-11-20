<?php

use Bl\Infrastructure\Pg\CastToPgBoolean;

/**
 * Returns a singleton CastToPgBoolean instance.
 */
function cast_to_pg_boolean(): CastToPgBoolean
{
    static $castToPgBoolean = null;

    if (null === $castToPgBoolean) {
        $castToPgBoolean = new CastToPgBoolean();
    }

    return $castToPgBoolean;
}
