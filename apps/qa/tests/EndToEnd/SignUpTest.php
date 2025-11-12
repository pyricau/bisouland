<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd;

use Bl\Qa\Tests\EndToEnd\Assertion\Assert;
use Bl\Qa\Tests\Infrastructure\Scenario\SignUpNewPlayer;
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
        $player = SignUpNewPlayer::run(
            'BisouTest',
            'password',
            'password',
        );

        Assert::signedUpCount($player->username, 1);
    }

    #[DataProvider('invalidCredentialsProvider')]
    #[TestDox('It prevents invalid credentials: $description')]
    public function test_it_prevents_invalid_credentials(string $username, string $password, string $description): void
    {
        SignUpNewPlayer::run(
            $username,
            $password,
            $password,
        );

        Assert::signedUpCount($username, 0);
    }

    /**
     * [string $username, string $password, string $description][].
     *
     * @return array<array{string, string, string}>
     */
    public static function invalidCredentialsProvider(): array
    {
        return [
            ['usr', 'password', 'username too short (< 4 characters)'],
            ['test_sign_up02__', 'password', 'username too long (> 15 characters)'],
            ['test_sign_up03!', 'password', 'username contains special characters (non alpha-numerical, not an underscore (`_`))'],
            ['test_sign_up05', 'pass', 'password too short (< 5 characters)'],
            ['test_sign_up06', 'passwordthatistoolong', 'password too long (> 15 characters)'],
            ['test_sign_up07', 'password!', 'password contains special characters (non alpha-numerical, not an underscore (`_`))'],
            ['BisouLand', 'password', 'system account, for notifications'],
        ];
    }

    #[TestDox('It prevents usernames that are already used')]
    public function test_it_prevents_usernames_that_are_already_used(): void
    {
        $username = 'BisouTest_';
        $password = 'password';
        $passwordConfirmation = $password;

        // First registration should succeed
        SignUpNewPlayer::run(
            $username,
            $password,
            $passwordConfirmation,
        );
        // Second registration should fail
        SignUpNewPlayer::run(
            $username,
            $password,
            $passwordConfirmation,
        );

        Assert::signedUpCount($username, 1);
    }

    public function test_it_prevents_passwords_that_do_not_match_confirmation(): void
    {
        $username = 'BisouTest';
        $password = 'password';
        $passwordConfirmation = 'different';

        SignUpNewPlayer::run(
            $username,
            $password,
            $passwordConfirmation,
        );

        Assert::signedUpCount($username, 0);
    }
}
