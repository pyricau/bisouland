<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Fixtures\AuthToken;

use Bl\Auth\AuthToken\TokenPlain;

final readonly class TokenPlainFixture
{
    public static function make(): TokenPlain
    {
        return TokenPlain::fromString(self::makeString());
    }

    public static function makeString(): string
    {
        return bin2hex(random_bytes(16));
    }
}
