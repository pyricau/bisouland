<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignInPlayer;

use Bl\Auth\Application\AuthToken\CreateAuthToken;
use Bl\Auth\Application\AuthTokenCookie\CreateAuthTokenCookie;
use Bl\Qa\Application\Action\ActionOutput;

/**
 * @object-type DataTransferObject
 */
final readonly class SignedInPlayer implements ActionOutput
{
    public function __construct(
        public CreateAuthToken $createAuthToken,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $cookie = CreateAuthTokenCookie::fromCreateAuthToken($this->createAuthToken);

        return [
            'auth_token_id' => $this->createAuthToken->authToken->authTokenId->toString(),
            'token_plain' => $this->createAuthToken->tokenPlain->toString(),
            'expires_at' => $this->createAuthToken->authToken->expiresAt->toString(),
            'cookie' => "{$cookie->getName()}={$cookie->getValue()}",
        ];
    }
}
