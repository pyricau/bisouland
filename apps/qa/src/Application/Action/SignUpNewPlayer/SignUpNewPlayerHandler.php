<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignUpNewPlayer;

use Bl\Auth\Account\PasswordPlain;
use Bl\Auth\Account\Username;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\SaveNewPlayer;

/**
 * @object-type UseCase
 */
final readonly class SignUpNewPlayerHandler
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
    public function run(SignUpNewPlayer $input): SignUpNewPlayerOutput
    {
        $player = $this->saveNewPlayer->save(
            Username::fromString($input->username),
            PasswordPlain::fromString($input->password),
        );

        return new SignUpNewPlayerOutput($player);
    }
}
