<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken;

use Bl\Domain\Auth\AuthToken\TokenHash;
use Bl\Domain\Exception\ValidationFailedException;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\TokenHashFixture;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\TokenPlainFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenHash::class)]
#[Small]
final class TokenHashTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringTokenHash = TokenHashFixture::makeString();
        $tokenHash = TokenHash::fromString($stringTokenHash);

        $this->assertInstanceOf(TokenHash::class, $tokenHash);
        $this->assertSame($stringTokenHash, $tokenHash->toString());
    }

    #[TestDox('It can be created from TokenPlain')]
    public function test_it_can_be_created_from_token_plain(): void
    {
        $tokenPlain = TokenPlainFixture::make();
        $stringTokenHash = TokenHashFixture::makeString($tokenPlain->toString());
        $tokenHash = TokenHash::fromTokenPlain($tokenPlain);

        $this->assertInstanceOf(TokenHash::class, $tokenHash);
        $this->assertSame($stringTokenHash, $tokenHash->toString());
    }

    #[TestDox('It fails when it $scenario')]
    #[DataProvider('invalidTokenHashProvider')]
    public function test_it_fails_when_it_is_not_sha256(
        string $scenario,
        string $invalidTokenHash,
    ): void {
        $this->expectException(ValidationFailedException::class);

        TokenHash::fromString($invalidTokenHash);
    }

    /**
     * @return \Iterator<int|string, array{string, string}>
     */
    public static function invalidTokenHashProvider(): \Iterator
    {
        yield ['is an empty string', ''];
        yield ['is shorter than 64 characters', 'not 64 characters'];
        yield ['is longer than 64 characters', 'this string is definitely way too long to be 64 hexadecimal characters'];
        yield ['has non hexadecimal characters', 'non hexadecimal characters!!!!!'];
    }
}
