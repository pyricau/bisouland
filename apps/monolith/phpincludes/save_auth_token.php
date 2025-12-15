<?php

use Bl\Domain\Auth\Service\SaveAuthToken;
use Bl\Infrastructure\Pg\Auth\Service\PdoSaveAuthToken;

/**
 * Returns a singleton SaveAuthToken instance.
 */
function save_auth_token(PDO $pdo): SaveAuthToken
{
    static $saveAuthToken = null;

    if (null === $saveAuthToken) {
        $saveAuthToken = new PdoSaveAuthToken($pdo);
    }

    return $saveAuthToken;
}
