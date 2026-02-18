<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;

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
