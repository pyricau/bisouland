<?php

declare(strict_types=1);

namespace Bl\Application\Auth\AuthTokenCookie;

use Bl\Application\Auth\AuthToken\CreateAuthToken;
use Bl\Domain\Auth\AuthToken\ExpiresAt;
use Bl\Domain\Auth\AuthTokenCookie\Credentials;

/**
 * @object-type DataTransferObject
 */
final readonly class CreateAuthTokenCookie
{
    public function __construct(
        public Credentials $credentials,
        public ExpiresAt $expiresAt,
    ) {
    }

    public function getName(): string
    {
        return Credentials::NAME;
    }

    public function getValue(): string
    {
        return $this->credentials->toCookie();
    }

    /**
     * @return array{
     *     expires: int, // UNIX timestamp
     *     httponly: true, // XSS protection (prevents usage from javascript)
     *     secure: true, // HTTPS only
     *     samesite: 'Strict', // CSRF protection
     *     path: '/',
     * }
     */
    public function getOptions(): array
    {
        return [
            'expires' => $this->expiresAt->toTimestamp(),
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict',
            'path' => '/',
        ];
    }

    public static function fromCreateAuthToken(CreateAuthToken $createAuthToken): self
    {
        return new self(
            new Credentials(
                $createAuthToken->authToken->authTokenId,
                $createAuthToken->tokenPlain,
            ),
            $createAuthToken->authToken->expiresAt,
        );
    }
}
