<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\EndToEnd;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\SignUpNewPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class SignUpTest extends TestCase
{
    public function test_it_allows_visitors_to_become_players(): void
    {
        // Arrange
        $pdo = TestKernelSingleton::get()->pdo();

        // Act
        $passwordPlain = PasswordPlainFixture::makeString();
        $username = SignUpNewPlayer::run(
            UsernameFixture::makeString(),
            $passwordPlain,
            $passwordPlain,
        );

        // Assert
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertSame(1, (int) $stmt->fetchColumn());
    }

    #[DataProvider('invalidCredentialsProvider')]
    #[TestDox('It prevents invalid credentials: $scenario')]
    public function test_it_prevents_invalid_credentials(
        string $scenario,
        string $username,
        string $passwordPlain,
    ): void {
        // Arrange
        $pdo = TestKernelSingleton::get()->pdo();

        // Act
        SignUpNewPlayer::run(
            $username,
            $passwordPlain,
            $passwordPlain,
        );

        // Assert
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertSame(0, (int) $stmt->fetchColumn());
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      username: string,
     *      passwordPlain: string,
     *  }>
     */
    public static function invalidCredentialsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username too short (< 4 characters)',
            'username' => 'usr',
            'passwordPlain' => PasswordPlainFixture::makeString(),
        ];
        yield [
            'scenario' => 'username too long (> 15 characters)',
            'username' => 'test_sign_up02__',
            'passwordPlain' => PasswordPlainFixture::makeString(),
        ];
        yield [
            'scenario' => 'username contains special characters (non alpha-numerical, not an underscore (`_`))',
            'username' => 'test_sign_up03!',
            'passwordPlain' => PasswordPlainFixture::makeString(),
        ];
        yield [
            'scenario' => 'password too short (< 5 characters)',
            'username' => UsernameFixture::makeString(),
            'passwordPlain' => 'pass',
        ];
        yield [
            'scenario' => 'password too long (> 15 characters)',
            'username' => UsernameFixture::makeString(),
            'passwordPlain' => 'passwordthatistoolong',
        ];
        yield [
            'scenario' => 'password contains special characters (non alpha-numerical, not an underscore (`_`))',
            'username' => UsernameFixture::makeString(),
            'passwordPlain' => 'password!',
        ];
    }

    #[TestDox('It prevents usernames that are already used')]
    public function test_it_prevents_usernames_that_are_already_used(): void
    {
        // Arrange
        $pdo = TestKernelSingleton::get()->pdo();
        $username = UsernameFixture::makeString();
        $passwordPlain = PasswordPlainFixture::makeString();

        // Act
        // First registration should succeed
        SignUpNewPlayer::run($username, $passwordPlain, $passwordPlain);
        // Second registration should fail
        SignUpNewPlayer::run($username, $passwordPlain, $passwordPlain);

        // Assert
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertSame(1, (int) $stmt->fetchColumn());
    }

    public function test_it_prevents_passwords_that_do_not_match_confirmation(): void
    {
        // Arrange
        $pdo = TestKernelSingleton::get()->pdo();
        $username = UsernameFixture::makeString();
        $passwordPlain = PasswordPlainFixture::makeString();

        // Act
        SignUpNewPlayer::run($username, $passwordPlain, 'different');

        // Assert
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertSame(0, (int) $stmt->fetchColumn());
    }
}
