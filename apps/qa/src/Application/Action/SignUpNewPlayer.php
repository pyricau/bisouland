<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action;

use Bl\Qa\Domain\Auth\Account\PasswordPlain;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\SaveNewPlayer;

/**
 * @object-type Action
 */
final readonly class SignUpNewPlayer
{
    public function __construct(
        private SaveNewPlayer $saveNewPlayer,
    ) {
    }

    /**
     * @throws ValidationFailedException If the username is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the password is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the username is already registered
     * @throws ValidationFailedException If cloud coordinates X/Y are already occupied (race condition)
     * @throws ValidationFailedException If no cloud coordinate Y is available
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(
        string $username,
        string $password,
    ): Player {
        return $this->saveNewPlayer->save(
            Username::fromString($username),
            PasswordPlain::fromString($password),
        );
    }
}
