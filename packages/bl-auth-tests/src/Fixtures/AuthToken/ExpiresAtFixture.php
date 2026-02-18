<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures\AuthToken;

use Bl\Auth\AuthToken\ExpiresAt;

final readonly class ExpiresAtFixture
{
    public static function make(): ExpiresAt
    {
        return ExpiresAt::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return new \DateTimeImmutable(ExpiresAt::DEFAULT_DURATION)->format('Y-m-d\TH:i:s.uP');
    }
}
