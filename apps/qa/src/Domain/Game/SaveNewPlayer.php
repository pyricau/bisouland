<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

use Bl\Qa\Domain\Auth\Account\PasswordPlain;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type Service
 *
 * Saves a new player:
 * - With username availability check
 * - With automatically determined cloud coordinates
 */
interface SaveNewPlayer
{
    /**
     * @throws ValidationFailedException If the username is already registered
     * @throws ValidationFailedException If cloud coordinates X/Y are already occupied (race condition)
     * @throws ValidationFailedException If no cloud coordinate Y is available
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function save(Username $username, PasswordPlain $password): Player;
}
