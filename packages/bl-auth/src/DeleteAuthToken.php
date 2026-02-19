<?php

declare(strict_types=1);

namespace Bl\Auth;

use Bl\Auth\Account\AccountId;
use Bl\Exception\ServerErrorException;

/**
 * @object-type Service
 */
interface DeleteAuthToken
{
    /**
     * @throws ServerErrorException If an unexpected error occurs
     */
    public function delete(AccountId $accountId): void;
}
