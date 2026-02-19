<?php

declare(strict_types=1);

namespace Bl\Auth;

use Bl\Auth\Account\AccountId;
use Bl\Auth\Account\PasswordHash;
use Bl\Auth\Account\Username;

/**
 * @object-type Entity
 */
final readonly class Account
{
    public function __construct(
        public AccountId $accountId,
        public Username $username,
        public PasswordHash $passwordHash,
    ) {
    }
}
