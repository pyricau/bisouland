<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke;

use Bl\Qa\Tests\Infrastructure\Scenario\GetLoggedInPlayer;
use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use Bl\Qa\Tests\Smoke\Assertion\Assert;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class PlayerPagesTest extends TestCase
{
    #[TestDox('it blocks $pageName page (`$url`) for visitors')]
    #[DataProvider('playerPagesProvider')]
    public function test_it_blocks_player_page_for_visitors(string $url, string $pageName): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', $url);

        Assert::blocksPageForLoggedOutVisitors($response);
    }

    #[TestDox('it loads $pageName page (`$url`) for logged in players')]
    #[DataProvider('playerPagesProvider')]
    public function test_it_loads_player_page_for_logged_in_players(string $url, string $pageName): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $loggedInPlayer = GetLoggedInPlayer::run();

        $response = $httpClient->request('GET', $url, [
            'headers' => [
                'Cookie' => $loggedInPlayer->sessionCookie,
            ],
        ]);

        Assert::loadsPageForLoggedInPlayers($response);
    }

    /**
     * @return array<array{string, string}>
     */
    public static function playerPagesProvider(): array
    {
        return [
            ['/connected.html', 'account'],
            ['/action.html', 'blow kisses'],
            ['/cerveau.html', 'brain'],
            ['/changepass.html', 'change password'],
            ['/nuage.html', 'clouds'],
            ['/yeux.html', 'eyes'],
            ['/boite.html', 'inbox'],
            ['/bisous.html', 'kisses'],
            ['/construction.html', 'organs'],
            ['/infos.html', 'reference'],
            ['/techno.html', 'techniques'],
            ['/lire.html', 'view message'],
        ];
    }
}
