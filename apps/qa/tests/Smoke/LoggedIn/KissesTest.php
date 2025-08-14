<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke\LoggedIn;

use Bl\Qa\Tests\Infrastructure\Scenario\DeletePlayer;
use Bl\Qa\Tests\Infrastructure\Scenario\LogInPlayer;
use Bl\Qa\Tests\Infrastructure\Scenario\SignUpNewPlayer;
use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use Bl\Qa\Tests\Smoke\Assertion\Assert;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class KissesTest extends TestCase
{
    #[TestDox('it blocks kisses page (`/bisous.html`) for visitors')]
    public function test_it_blocks_kisses_page_for_visitors(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/bisous.html');

        Assert::blocksPageForLoggedOutVisitors($response);
    }

    #[TestDox('it loads kisses page (`/bisous.html`) for logged in players')]
    public function test_it_loads_kisses_page_for_logged_in_players(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        // Create a test player and log them in
        $player = SignUpNewPlayer::run();
        $sessionCookie = LogInPlayer::run($player);

        $response = $httpClient->request('GET', '/bisous.html', [
            'headers' => [
                'Cookie' => $sessionCookie,
            ],
        ]);

        // Clean up test data
        DeletePlayer::run($player);

        Assert::loadsPageForLoggedInPlayers($response);
    }
}
