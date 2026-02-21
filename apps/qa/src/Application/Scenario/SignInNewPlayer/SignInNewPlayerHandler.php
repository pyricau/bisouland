<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Scenario\SignInNewPlayer;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayer;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerHandler;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerHandler;

/**
 * @object-type UseCase
 */
final readonly class SignInNewPlayerHandler
{
    public function __construct(
        private SignUpNewPlayerHandler $signUpNewPlayerHandler,
        private SignInPlayerHandler $signInPlayerHandler,
    ) {
    }

    /**
     * @throws ValidationFailedException If the username is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the password is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the username is already registered
     * @throws ValidationFailedException If cloud coordinates X/Y are already occupied (race condition)
     * @throws ValidationFailedException If no cloud coordinate Y is available
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(SignInNewPlayer $input): SignInNewPlayerOutput
    {
        $signedUp = $this->signUpNewPlayerHandler->run(
            new SignUpNewPlayer($input->username, $input->password),
        );

        $signedIn = $this->signInPlayerHandler->run(
            new SignInPlayer($signedUp->player->account->username->toString()),
        );

        return new SignInNewPlayerOutput($signedUp, $signedIn);
    }
}
