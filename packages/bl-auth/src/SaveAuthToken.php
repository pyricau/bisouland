<?php

declare(strict_types=1);

namespace Bl\Auth;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;

/**
 * @object-type Service
 */
interface SaveAuthToken
{
    /**
     * @throws ValidationFailedException If the account ID does not exist
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function save(AuthToken $authToken): void;
}
