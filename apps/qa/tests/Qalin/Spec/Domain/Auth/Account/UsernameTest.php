<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Auth\Account;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Username::class)]
#[Small]
final class UsernameTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringUsername = UsernameFixture::makeString();
        $username = Username::fromString($stringUsername);

        $this->assertInstanceOf(Username::class, $username);
        $this->assertSame($stringUsername, $username->toString());
    }

    #[TestDox('It fails when raw username $scenario')]
    #[DataProvider('invalidUsernameProvider')]
    public function test_it_fails_when_raw_username_is_invalid(
        string $scenario,
        string $invalidUsername,
    ): void {
        $this->expectException(ValidationFailedException::class);

        Username::fromString($invalidUsername);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      invalidUsername: string,
     *  }>
     */
    public static function invalidUsernameProvider(): \Iterator
    {
        yield ['scenario' => 'is empty', 'invalidUsername' => ''];
        yield ['scenario' => 'too short (< 4 characters)', 'invalidUsername' => 'abc'];
        yield ['scenario' => 'too long (> 15 characters)', 'invalidUsername' => 'abcdefghijklmnop'];
        yield ['scenario' => 'contains special characters (non alpha-numerical, not an underscore (`_`))', 'invalidUsername' => 'user@name'];
    }
}
