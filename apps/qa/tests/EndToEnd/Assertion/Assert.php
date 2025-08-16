<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd\Assertion;

use Bl\Qa\Tests\Infrastructure\Scenario\LoggedInPlayer;
use Bl\Qa\Tests\Infrastructure\Scenario\Player;
use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Assert as PHPUnitAssert;

final readonly class Assert
{
    public static function playerIsLoggedIn(LoggedInPlayer $loggedInPlayer): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $response = $httpClient->request('GET', '/cerveau.html', [
            'headers' => [
                'Cookie' => $loggedInPlayer->sessionCookie,
            ],
        ]);
        $content = (string) $response->getContent();

        if (str_contains($content, "D&eacute;connexion ({$loggedInPlayer->username})")) {
            PHPUnitAssert::assertSame(200, $response->getStatusCode(), $content);

            return;
        }

        PHPUnitAssert::fail("Failed asserting that Player is logged in. Content: {$content}");
    }

    public static function playerLovePoints(Player $player, int $expectedLovePoints): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('SELECT amour FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $player->username,
        ]);
        $actualLovePoints = (int) $stmt->fetchColumn();

        if ($expectedLovePoints === $actualLovePoints) {
            PHPUnitAssert::assertSame($expectedLovePoints, $actualLovePoints);

            return;
        }

        PHPUnitAssert::fail("Failed asserting that Player's Love Points {$actualLovePoints} is {$expectedLovePoints}");
    }

    public static function playerCloud(Player $player, int $expectedCloud): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('SELECT nuage FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $player->username,
        ]);
        $actualCloud = (int) $stmt->fetchColumn();

        if ($expectedCloud === $actualCloud) {
            PHPUnitAssert::assertSame($expectedCloud, $actualCloud);

            return;
        }

        PHPUnitAssert::fail("Failed asserting that Player's Cloud {$actualCloud} is {$expectedCloud}");
    }

    public static function playerNotified(Player $player, string $expectedTitle): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare(<<<'SQL'
            SELECT messages.titre
            FROM messages
            INNER JOIN membres ON messages.destin = membres.id
            WHERE membres.pseudo = :username
            ORDER BY messages.timestamp DESC
            LIMIT 1
        SQL);
        $stmt->execute([
            'username' => $player->username,
        ]);
        $actualTitle = $stmt->fetchColumn();

        if ($expectedTitle === $actualTitle) {
            PHPUnitAssert::assertSame($expectedTitle, $actualTitle);

            return;
        }

        PHPUnitAssert::fail("Failed asserting that Player's notification '{$actualTitle}' is '{$expectedTitle}'");
    }

    public static function signedUpCount(string $username, int $expectedCount): void
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $actualCount = (int) $stmt->fetchColumn();

        if ($expectedCount === $actualCount) {
            PHPUnitAssert::assertSame($expectedCount, $actualCount);

            return;
        }

        PHPUnitAssert::fail("Failed asserting that Signed Up Count {$actualCount} is {$expectedCount}");
    }
}
