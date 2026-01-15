<?php

declare(strict_types=1);

use Bl\Infrastructure\Pg\CastToPgTimestamptz;

/**
 * Returns a singleton CastToPgTimestamptz instance.
 */
function cast_to_pg_timestamptz(): CastToPgTimestamptz
{
    static $castToPgTimestamptz = null;

    if (null === $castToPgTimestamptz) {
        $castToPgTimestamptz = new CastToPgTimestamptz();
    }

    return $castToPgTimestamptz;
}
