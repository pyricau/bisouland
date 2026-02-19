<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Account\Username;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Game\ApplyCompletedUpgrade;
use Bl\Game\FindPlayer;
use Bl\Game\Player\UpgradableLevels\Upgradable;
use Bl\Game\Tests\Fixtures\Player\UpgradableLevels\UpgradableFixture;
use Bl\Game\Tests\Fixtures\PlayerFixture;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgrade;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeHandler;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeOutput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(InstantFreeUpgradeHandler::class)]
#[Small]
final class InstantFreeUpgradeHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     levels: int,
     * }>
     */
    public static function levelsProvider(): \Iterator
    {
        yield ['scenario' => '1 level', 'levels' => 1];
        yield ['scenario' => '3 levels', 'levels' => 3];
    }

    #[TestDox('It provides an instant free upgrade for $scenario')]
    #[DataProvider('levelsProvider')]
    public function test_it_provides_an_instant_free_upgrade_for_a_given_username_and_upgradable(
        string $scenario,
        int $levels,
    ): void {
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $player = PlayerFixture::make();
        $expectedPlayer = PlayerFixture::make();

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::type(Username::class),
        )->willReturn($player);

        $applyCompletedUpgrade = $this->prophesize(ApplyCompletedUpgrade::class);
        $applyCompletedUpgrade->apply(
            Argument::type(Username::class),
            Argument::type(Upgradable::class),
            Argument::type('int'), // milli_score
        )->shouldBeCalledTimes($levels)->willReturn($expectedPlayer);

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            $applyCompletedUpgrade->reveal(),
            $findPlayer->reveal(),
        );
        $output = $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));

        $this->assertInstanceOf(InstantFreeUpgradeOutput::class, $output);
        $this->assertSame($expectedPlayer, $output->player);
    }

    public function test_it_fails_when_levels_is_less_than_1(): void
    {
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $levels = 0;

        $findPlayer = $this->prophesize(FindPlayer::class);
        $applyCompletedUpgrade = $this->prophesize(ApplyCompletedUpgrade::class);

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            $applyCompletedUpgrade->reveal(),
            $findPlayer->reveal(),
        );

        $this->expectException(ValidationFailedException::class);
        $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));
    }

    public function test_it_fails_when_username_is_not_an_existing_one(): void
    {
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $levels = 1;

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::type(Username::class),
        )->willThrow(ValidationFailedException::class);

        $applyCompletedUpgrade = $this->prophesize(ApplyCompletedUpgrade::class);

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            $applyCompletedUpgrade->reveal(),
            $findPlayer->reveal(),
        );

        $this->expectException(ValidationFailedException::class);
        $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));
    }

    public function test_it_fails_when_apply_completed_upgrade_fails(): void
    {
        $username = UsernameFixture::makeString();
        $upgradable = UpgradableFixture::makeString();
        $levels = 1;
        $foundPlayer = PlayerFixture::make();

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::type(Username::class),
        )->willReturn($foundPlayer);

        $applyCompletedUpgrade = $this->prophesize(ApplyCompletedUpgrade::class);
        $applyCompletedUpgrade->apply(
            Argument::type(Username::class),
            Argument::type(Upgradable::class),
            Argument::type('int'),
        )->willThrow(ServerErrorException::class);

        $instantFreeUpgradeHandler = new InstantFreeUpgradeHandler(
            $applyCompletedUpgrade->reveal(),
            $findPlayer->reveal(),
        );

        $this->expectException(ServerErrorException::class);
        $instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));
    }
}
