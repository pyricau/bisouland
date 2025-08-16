<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd\OnSignUp;

use Bl\Qa\Tests\EndToEnd\Assertion\Assert;
use Bl\Qa\Tests\Infrastructure\Scenario\LoggedInPlayer;
use Bl\Qa\Tests\Infrastructure\Scenario\LogInPlayer;
use Bl\Qa\Tests\Infrastructure\Scenario\SignUpNewPlayer;
use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class NewPlayerTest extends TestCase
{
    public function test_it_can_log_in(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $player = SignUpNewPlayer::run(
            'BisouTest',
            'password',
            'password',
        );
        $sessionCookie = LogInPlayer::run($player);
        $loggedInPlayer = new LoggedInPlayer($player->username, $player->password, $sessionCookie);

        Assert::playerIsLoggedIn($loggedInPlayer);
    }

    public function test_it_gets_an_initial_300_love_points(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $player = SignUpNewPlayer::run(
            'BisouTest',
            'password',
            'password',
        );

        Assert::playerLovePoints($player, 300);
    }

    public function test_it_gets_placed_in_clouds(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $player = SignUpNewPlayer::run(
            'BisouTest',
            'password',
            'password',
        );

        Assert::playerCloud($player, 1);
    }
}
