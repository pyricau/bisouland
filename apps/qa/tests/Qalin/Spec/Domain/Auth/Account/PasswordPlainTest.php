<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Auth\Account;

use Bl\Qa\Domain\Auth\Account\PasswordPlain;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordPlainFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordPlain::class)]
#[Small]
final class PasswordPlainTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringPasswordPlain = PasswordPlainFixture::makeString();
        $passwordPlain = PasswordPlain::fromString($stringPasswordPlain);

        $this->assertInstanceOf(PasswordPlain::class, $passwordPlain);
        $this->assertSame($stringPasswordPlain, $passwordPlain->toString());
    }

    #[TestDox('It fails when raw password $scenario')]
    #[DataProvider('invalidPasswordPlainProvider')]
    public function test_it_fails_when_raw_password_is_invalid(
        string $scenario,
        string $invalidPasswordPlain,
    ): void {
        $this->expectException(ValidationFailedException::class);

        PasswordPlain::fromString($invalidPasswordPlain);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidPasswordPlain: string,
     *  }>
     */
    public static function invalidPasswordPlainProvider(): \Iterator
    {
        yield ['scenario' => 'is empty', 'invalidPasswordPlain' => ''];
        yield ['scenario' => 'too short (< 8 characters)', 'invalidPasswordPlain' => 'abcdefg'];
    }
}
