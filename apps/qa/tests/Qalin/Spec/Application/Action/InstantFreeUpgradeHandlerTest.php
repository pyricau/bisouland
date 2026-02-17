<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgrade;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeHandler;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeOutput;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevels\UpgradableFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(InstantFreeUpgradeHandler::class)]
#[Small]
final class InstantFreeUpgradeHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_runs_action_successfully(): void
    {
        // TODO: set up test doubles and fixtures
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $levels = 1; // TODO: use fixture

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            // TODO: inject revealed prophecies
        );
        $output = $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));

        $this->assertInstanceOf(InstantFreeUpgradeOutput::class, $output);
        // TODO: add assertions on $output->player
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    #[TestDox('It fails when $scenario')]
    #[DataProvider('failureProvider')]
    public function test_it_fails_when_an_error_occurs(
        string $scenario,
        string $exception,
    ): void {
        // TODO: set up test doubles
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $levels = 1; // TODO: use fixture

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            // TODO: inject revealed prophecies
        );

        $this->expectException($exception);
        $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      exception: class-string<\Throwable>,
     *  }>
     */
    public static function failureProvider(): \Iterator
    {
        yield ['scenario' => 'validation fails', 'exception' => ValidationFailedException::class];
        yield ['scenario' => 'an unexpected error occurs', 'exception' => ServerErrorException::class];
    }
}
