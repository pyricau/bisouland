<?php

use Bl\Domain\Auth\Factory\TokenFactory;
use Bl\Infrastructure\Php\Auth\Factory\RandomBytesTokenFactory;

/**
 * Returns a singleton TokenFactory instance.
 */
function token_factory(): TokenFactory
{
    static $tokenFactory = null;

    if (null === $tokenFactory) {
        $tokenFactory = new RandomBytesTokenFactory();
    }

    return $tokenFactory;
}
