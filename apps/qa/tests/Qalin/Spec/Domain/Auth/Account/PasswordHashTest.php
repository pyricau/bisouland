<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Auth\Account;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordHashFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordPlainFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordHash::class)]
#[Small]
final class PasswordHashTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringPasswordHash = PasswordHashFixture::makeString();
        $passwordHash = PasswordHash::fromString($stringPasswordHash);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertSame($stringPasswordHash, $passwordHash->toString());
    }

    #[TestDox('It can be created from a PasswordPlain')]
    public function test_it_can_be_created_from_a_password_plain(): void
    {
        $passwordPlain = PasswordPlainFixture::make();
        $passwordHash = PasswordHash::fromPasswordPlain($passwordPlain);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertTrue(password_verify($passwordPlain->toString(), $passwordHash->toString()));
    }

    #[TestDox('It fails when raw password hash $scenario')]
    #[DataProvider('invalidPasswordHashProvider')]
    public function test_it_fails_when_raw_password_hash_is_invalid(
        string $scenario,
        string $invalidPasswordHash,
    ): void {
        $this->expectException(ValidationFailedException::class);

        PasswordHash::fromString($invalidPasswordHash);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidPasswordHash: string,
     *  }>
     */
    public static function invalidPasswordHashProvider(): \Iterator
    {
        yield ['scenario' => 'is empty', 'invalidPasswordHash' => ''];
        yield ['scenario' => 'is not a recognized password hash', 'invalidPasswordHash' => 'not-a-valid-hash'];
    }
}
