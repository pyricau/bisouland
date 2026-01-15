<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures;

use Bl\Domain\Auth\AuthToken\TokenPlain;

final readonly class TokenPlainFixture
{
    public static function make(): TokenPlain
    {
        $value = self::makeString();

        return TokenPlain::fromString($value);
    }

    public static function makeString(): string
    {
        return bin2hex(random_bytes(16));
    }
}
