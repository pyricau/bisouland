<?php

declare(strict_types=1);

namespace Bl\Auth\Tests\Spec\AuthToken;

use Bl\Auth\AuthToken\TokenPlain;
use Bl\Auth\Tests\Fixtures\AuthToken\TokenPlainFixture;
use Bl\Exception\ValidationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenPlain::class)]
#[Small]
final class TokenPlainTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringTokenPlain = TokenPlainFixture::makeString();
        $tokenPlain = TokenPlain::fromString($stringTokenPlain);

        $this->assertInstanceOf(TokenPlain::class, $tokenPlain);
        $this->assertSame($stringTokenPlain, $tokenPlain->toString());
    }

    #[TestDox('It generates unique 32-character hexadecimal tokens')]
    public function test_it_generates_unique_32_character_hexadecimal_tokens(): void
    {
        $tokenPlain1 = TokenPlain::create();
        $tokenPlain2 = TokenPlain::create();

        // If fromString doesn't throw, the format is valid
        TokenPlain::fromString($tokenPlain1->toString());
        TokenPlain::fromString($tokenPlain2->toString());

        $this->assertInstanceOf(TokenPlain::class, $tokenPlain1);
        $this->assertInstanceOf(TokenPlain::class, $tokenPlain2);
        $this->assertNotSame($tokenPlain1->toString(), $tokenPlain2->toString());
    }

    #[TestDox('It fails when it $scenario')]
    #[DataProvider('invalidTokenPlainProvider')]
    public function test_it_fails_when_it_is_not_32_hexadecimal_characters(
        string $scenario,
        string $invalidTokenPlain,
    ): void {
        $this->expectException(ValidationFailedException::class);

        TokenPlain::fromString($invalidTokenPlain);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidTokenPlain: string,
     *  }>
     */
    public static function invalidTokenPlainProvider(): \Iterator
    {
        yield ['scenario' => 'is an empty string', 'invalidTokenPlain' => ''];
        yield ['scenario' => 'is shorter than 32 characters', 'invalidTokenPlain' => 'not 32 characters'];
        yield ['scenario' => 'is longer than 32 characters', 'invalidTokenPlain' => 'this string is definitely way too long to be 32 hexadecimal characters'];
        yield ['scenario' => 'has non hexadecimal characters', 'invalidTokenPlain' => 'non hexadecimal characters!!!!!'];
    }
}
