<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\EndToEnd;

use Bl\Qa\Tests\Monolith\EndToEnd\Assertion\Assert;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\GetLoggedInPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\LogOutPlayer;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class LogOutTest extends TestCase
{
    public function test_it_allows_players_to_log_out(): void
    {
        $loggedInPlayer = GetLoggedInPlayer::run();

        Assert::playerIsLoggedIn($loggedInPlayer);

        LogOutPlayer::run($loggedInPlayer);

        Assert::playerIsLoggedOut($loggedInPlayer);
    }
}
