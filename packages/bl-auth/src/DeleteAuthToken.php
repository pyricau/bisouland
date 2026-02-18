<?php

declare(strict_types=1);

namespace Bl\Auth;

use Bl\Auth\Account\AccountId;

/**
 * @object-type Service
 */
interface DeleteAuthToken
{
    public function delete(AccountId $accountId): void;
}
