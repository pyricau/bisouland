<?php

declare(strict_types=1);

namespace Bl\Domain\Auth;

use Bl\Domain\Auth\Account\AccountId;

interface DeleteAuthToken
{
    public function delete(AccountId $accountId): void;
}
