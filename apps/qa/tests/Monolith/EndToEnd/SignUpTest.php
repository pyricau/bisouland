<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\EndToEnd;

use Bl\Qa\Tests\Monolith\EndToEnd\Assertion\Assert;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\SignUpNewPlayer;
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
    #[TestDox('It prevents invalid credentials: $scenario')]
    public function test_it_prevents_invalid_credentials(string $scenario, string $username, string $password): void
    {
        SignUpNewPlayer::run(
            $username,
            $password,
            $password,
        );

        Assert::signedUpCount($username, 0);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      username: string,
     *      password: string,
     *  }>
     */
    public static function invalidCredentialsProvider(): \Iterator
    {
        yield ['scenario' => 'username too short (< 4 characters)', 'username' => 'usr', 'password' => 'password'];
        yield ['scenario' => 'username too long (> 15 characters)', 'username' => 'test_sign_up02__', 'password' => 'password'];
        yield ['scenario' => 'username contains special characters (non alpha-numerical, not an underscore (`_`))', 'username' => 'test_sign_up03!', 'password' => 'password'];
        yield ['scenario' => 'password too short (< 5 characters)', 'username' => 'test_sign_up05', 'password' => 'pass'];
        yield ['scenario' => 'password too long (> 15 characters)', 'username' => 'test_sign_up06', 'password' => 'passwordthatistoolong'];
        yield ['scenario' => 'password contains special characters (non alpha-numerical, not an underscore (`_`))', 'username' => 'test_sign_up07', 'password' => 'password!'];
        yield ['scenario' => 'system account, for notifications', 'username' => 'BisouLand', 'password' => 'password'];
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
