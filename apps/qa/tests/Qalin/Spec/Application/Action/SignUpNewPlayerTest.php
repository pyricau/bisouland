<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SignUpNewPlayer::class)]
#[Small]
final class SignUpNewPlayerTest extends TestCase
{
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        string $username,
        string $password,
    ): void {
        $signUpNewPlayer = new SignUpNewPlayer($username, $password);

        $this->assertSame($username, $signUpNewPlayer->username);
        $this->assertSame($password, $signUpNewPlayer->password);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     username: string,
     *     password: string,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
        yield ['scenario' => 'username as a required parameter', 'username' => UsernameFixture::makeString(), 'password' => PasswordPlainFixture::makeString()];
        yield ['scenario' => 'password as a required parameter', 'username' => UsernameFixture::makeString(), 'password' => PasswordPlainFixture::makeString()];
    }
}
