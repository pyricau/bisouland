<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignInPlayer;

use Bl\Auth\Account\Username;
use Bl\Auth\Application\AuthToken\CreateAuthToken;
use Bl\Auth\SaveAuthToken;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Game\FindPlayer;

/**
 * @object-type UseCase
 */
final readonly class SignInPlayerHandler
{
    public function __construct(
        private FindPlayer $findPlayer,
        private SaveAuthToken $saveAuthToken,
    ) {
    }

    /**
     * @throws ValidationFailedException If the username is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(SignInPlayer $input): SignedInPlayer
    {
        $player = $this->findPlayer->find(
            Username::fromString($input->username),
        );

        $createAuthToken = CreateAuthToken::fromRawAccountId(
            $player->account->accountId->toString(),
        );

        $this->saveAuthToken->save($createAuthToken->authToken);

        return new SignedInPlayer($createAuthToken);
    }
}
