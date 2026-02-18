<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures\AuthToken;

use Bl\Auth\AuthToken\AuthTokenId;
use Symfony\Component\Uid\Uuid;

final readonly class AuthTokenIdFixture
{
    public static function make(): AuthTokenId
    {
        return AuthTokenId::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return Uuid::v7()->toString();
    }
}
