<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Auth;

use Bl\Qa\Domain\Auth\Account\AccountId;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Domain\Auth\Account\Username;

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
