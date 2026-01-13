<?php

declare(strict_types=1);

namespace Bl\Application\Auth\AuthTokenCookie;

use Bl\Domain\Auth\AuthTokenCookie\Credentials;

/**
 * @object-type DataTransferObject
 */
final readonly class RemoveAuthTokenCookie
{
    public function getName(): string
    {
        return Credentials::NAME;
    }

    public function getValue(): string
    {
        return '';
    }

    /**
     * @return array{
     *     expires: int, // UNIX timestamp in the past
     *     httponly: true, // XSS protection (prevents usage from javascript)
     *     secure: true, // HTTPS only
     *     samesite: 'Strict', // CSRF protection
     *     path: '/',
     * }
     */
    public function getOptions(): array
    {
        return [
            'expires' => 1,
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict',
            'path' => '/',
        ];
    }
}
