<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures;

use Bl\Domain\Auth\AuthToken\AuthTokenId;
use Symfony\Component\Uid\Uuid;

final readonly class AuthTokenIdFixture
{
    public static function make(): AuthTokenId
    {
        $value = self::makeString();

        return AuthTokenId::fromString($value);
    }

    public static function makeString(): string
    {
        return Uuid::v7()->toString();
    }
}
