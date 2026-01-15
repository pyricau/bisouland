<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures;

use Bl\Domain\Auth\AuthToken\ExpiresAt;

final readonly class ExpiresAtFixture
{
    public static function make(): ExpiresAt
    {
        $value = self::makeString();

        return ExpiresAt::fromString($value);
    }

    public static function makeString(): string
    {
        return new \DateTimeImmutable(ExpiresAt::DEFAULT_DURATION)->format('Y-m-d\TH:i:s.uP');
    }
}
