<?php

declare(strict_types=1);

use Bl\Infrastructure\Pg\CastToUnixTimestamp;

/**
 * Returns a singleton CastToUnixTimestamp instance.
 */
function cast_to_unix_timestamp(): CastToUnixTimestamp
{
    static $castToUnixTimestamp = null;

    if (null === $castToUnixTimestamp) {
        $castToUnixTimestamp = new CastToUnixTimestamp();
    }

    return $castToUnixTimestamp;
}
