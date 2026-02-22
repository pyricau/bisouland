<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\EndToEnd\OnSignUp;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\LogInPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\SignUpNewPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class NewPlayerTest extends TestCase
{
    public function test_it_can_log_in(): void
    {
        // Arrange
        $httpClient = TestKernelSingleton::get()->httpClient();

        // Act
        $passwordPlain = PasswordPlainFixture::makeString();
        $username = SignUpNewPlayer::run(
            UsernameFixture::makeString(),
            $passwordPlain,
            $passwordPlain,
        );
        $sessionCookie = LogInPlayer::run($username, $passwordPlain);

        // Assert
        $response = $httpClient->request('GET', '/cerveau.html', [
            'headers' => ['Cookie' => $sessionCookie],
        ]);
        $content = $response->getContent();

        $this->assertStringContainsString("D&eacute;connexion ({$username})", $content);
        $this->assertSame(200, $response->getStatusCode(), $content);
    }

    public function test_it_gets_an_initial_300_love_points(): void
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
        $stmt = $pdo->prepare('SELECT amour FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertSame(300, (int) $stmt->fetchColumn());
    }

    public function test_it_gets_placed_in_clouds(): void
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
        $stmt = $pdo->prepare('SELECT nuage FROM membres WHERE pseudo = :username');
        $stmt->execute(['username' => $username]);

        $this->assertGreaterThanOrEqual(1, (int) $stmt->fetchColumn());
    }

    public function test_it_receives_a_welcome_notification(): void
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
        $stmt = $pdo->prepare(<<<'SQL'
            SELECT notifications.title
            FROM notifications
            INNER JOIN membres ON notifications.account_id = membres.id
            WHERE membres.pseudo = :username
            ORDER BY notifications.notification_id DESC
            LIMIT 1
        SQL);
        $stmt->execute(['username' => $username]);

        $this->assertSame('Bienvenue sur BisouLand', $stmt->fetchColumn());
    }
}
