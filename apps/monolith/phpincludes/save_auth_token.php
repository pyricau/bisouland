<?php

declare(strict_types=1);

use Bl\Domain\Auth\SaveAuthToken;
use Bl\Infrastructure\Pg\Auth\PdoSaveAuthToken;

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
