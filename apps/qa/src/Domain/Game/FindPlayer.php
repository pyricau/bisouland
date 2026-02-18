<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

use Bl\Auth\Account\Username;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;

/**
 * @object-type Service
 */
interface FindPlayer
{
    /**
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function find(Username $username): Player;
}
