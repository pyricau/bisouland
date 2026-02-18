<?php

declare(strict_types=1);

use Bl\Auth\DeleteAuthToken;
use Bl\Auth\PdoPg\PdoPgDeleteAuthToken;

/**
 * Returns a singleton DeleteAuthToken instance.
 */
function delete_auth_token(PDO $pdo): DeleteAuthToken
{
    static $deleteAuthToken = null;

    if (null === $deleteAuthToken) {
        $deleteAuthToken = new PdoPgDeleteAuthToken($pdo);
    }

    return $deleteAuthToken;
}
