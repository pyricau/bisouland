<?php

declare(strict_types=1);

use Bl\Auth\PdoPg\PdoPgSaveAuthToken;
use Bl\Auth\SaveAuthToken;

/**
 * Returns a singleton SaveAuthToken instance.
 */
function save_auth_token(PDO $pdo): SaveAuthToken
{
    static $saveAuthToken = null;

    if (null === $saveAuthToken) {
        $saveAuthToken = new PdoPgSaveAuthToken($pdo);
    }

    return $saveAuthToken;
}
