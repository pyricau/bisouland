<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\EndToEnd;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignedInNewPlayer;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class LogOutTest extends TestCase
{
    public function test_it_allows_players_to_log_out(): void
    {
        // Arrange
        $httpClient = TestKernelSingleton::get()->httpClient();
        $scenarioRunner = TestKernelSingleton::get()->scenarioRunner();

        /** @var SignedInNewPlayer $signedInNewPlayer */
        $signedInNewPlayer = $scenarioRunner->run(new SignInNewPlayer(
            UsernameFixture::makeString(),
            PasswordPlainFixture::makeString(),
        ));

        $sessionCookie = $signedInNewPlayer->toArray()['cookie'];

        // Act
        $httpClient->request('GET', '/logout.html', [
            'headers' => ['Cookie' => $sessionCookie],
        ]);

        // Assert
        $response = $httpClient->request('GET', '/cerveau.html', [
            'headers' => ['Cookie' => $sessionCookie],
        ]);
        $content = $response->getContent();

        $this->assertStringContainsString("Tu n'es pas connect&eacute;.", $content);
        $this->assertSame(200, $response->getStatusCode(), $content);
    }
}
