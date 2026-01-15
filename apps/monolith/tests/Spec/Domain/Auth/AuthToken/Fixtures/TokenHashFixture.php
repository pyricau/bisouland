<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures;

use Bl\Domain\Auth\AuthToken\TokenHash;

final readonly class TokenHashFixture
{
    public const string GENERATE_VALUE = 'GENERATE_VALUE';

    public static function make(): TokenHash
    {
        $value = self::makeString();

        return TokenHash::fromString($value);
    }

    public static function makeString(string $stringTokenPlain = self::GENERATE_VALUE): string
    {
        if (self::GENERATE_VALUE === $stringTokenPlain) {
            $stringTokenPlain = TokenPlainFixture::makeString();
        }

        return hash('sha256', $stringTokenPlain);
    }
}
