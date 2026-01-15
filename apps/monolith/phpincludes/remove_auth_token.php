<?php

declare(strict_types=1);

use Bl\Domain\Auth\DeleteAuthToken;
use Bl\Infrastructure\Pg\Auth\PdoDeleteAuthToken;

/**
 * Returns a singleton DeleteAuthToken instance.
 */
function delete_auth_token(PDO $pdo): DeleteAuthToken
{
    static $deleteAuthToken = null;

    if (null === $deleteAuthToken) {
        $deleteAuthToken = new PdoDeleteAuthToken($pdo);
    }

    return $deleteAuthToken;
}
