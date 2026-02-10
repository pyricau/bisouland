<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Smoke;

use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\GetLoggedInPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\Tests\Monolith\Smoke\Assertion\Assert;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class PlayerPagesTest extends TestCase
{
    #[TestDox('It blocks $pageName page (`$url`) for visitors')]
    #[DataProvider('playerPagesProvider')]
    public function test_it_blocks_player_page_for_visitors(string $url, string $pageName): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', $url);

        Assert::blocksPageForLoggedOutVisitors($response);
    }

    #[TestDox('It loads $pageName page (`$url`) for logged in players')]
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
        Assert::noPhpErrorsOrWarnings($response);
    }

    /**
     * @return \Iterator<array{
     *      url: string,
     *      pageName: string,
     *  }>
     */
    public static function playerPagesProvider(): \Iterator
    {
        yield ['url' => '/connected.html', 'pageName' => 'account'];
        yield ['url' => '/action.html', 'pageName' => 'blow kisses'];
        yield ['url' => '/cerveau.html', 'pageName' => 'brain'];
        yield ['url' => '/changepass.html', 'pageName' => 'change password'];
        yield ['url' => '/nuage.html', 'pageName' => 'clouds'];
        yield ['url' => '/yeux.html', 'pageName' => 'eyes'];
        yield ['url' => '/boite.html', 'pageName' => 'inbox'];
        yield ['url' => '/bisous.html', 'pageName' => 'kisses'];
        yield ['url' => '/construction.html', 'pageName' => 'organs'];
        yield ['url' => '/infos.html', 'pageName' => 'reference'];
        yield ['url' => '/techno.html', 'pageName' => 'techniques'];
        yield ['url' => '/lire.html', 'pageName' => 'view message'];
    }
}
