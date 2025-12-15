<?php

use Bl\Domain\Auth\Factory\AuthTokenIdFactory;
use Bl\Infrastructure\Symfony\Auth\Factory\UuidAuthTokenIdFactory;

/**
 * Returns a singleton AuthTokenIdFactory instance.
 */
function auth_token_id_factory(): AuthTokenIdFactory
{
    static $authTokenIdFactory = null;

    if (null === $authTokenIdFactory) {
        $authTokenIdFactory = new UuidAuthTokenIdFactory();
    }

    return $authTokenIdFactory;
}
