<?php

declare(strict_types=1);

namespace Bl\Domain\Auth\AuthTokenCookie;

use Bl\Domain\Auth\AuthToken\AuthTokenId;
use Bl\Domain\Auth\AuthToken\TokenPlain;
use Bl\Domain\Exception\ValidationFailedException;

final readonly class Credentials
{
    public const string NAME = 'bl_auth_token';

    public function __construct(
        public AuthTokenId $authTokenId,
        public TokenPlain $tokenPlain,
    ) {
    }

    /**
     * @throws ValidationFailedException If cookie doesn't follow the format auth_token_id:token_plain
     * @throws ValidationFailedException If AuthTokenId isn't valid
     * @throws ValidationFailedException If Token isn't valid
     */
    public static function fromCookie(string $cookie): self
    {
        if (1 !== substr_count($cookie, ':')) {
            // Don't include $cookie value in message to avoid leaking credentials
            throw ValidationFailedException::make('Invalid "cookie" parameter: it should follow the `auth_token_id:token_plain` format');
        }

        [$stringAuthTokenId, $stringToken] = explode(':', $cookie, 2);

        return new self(
            AuthTokenId::fromString($stringAuthTokenId),
            TokenPlain::fromString($stringToken),
        );
    }

    /**
     * @return string auth_token_id:token_plain
     */
    public function toCookie(): string
    {
        return "{$this->authTokenId->toString()}:{$this->tokenPlain->toString()}";
    }
}
