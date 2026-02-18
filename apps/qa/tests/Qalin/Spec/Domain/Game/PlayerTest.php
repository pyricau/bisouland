<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game;

use Bl\Auth\Tests\Fixtures\AccountFixture;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\CloudCoordinatesFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\LovePointsFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\MilliScoreFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevelsFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\PlayerFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Player::class)]
#[Small]
final class PlayerTest extends TestCase
{
    #[TestDox('It has Account')]
    public function test_it_has_account(): void
    {
        $account = AccountFixture::make();
        $player = PlayerFixture::make(account: $account);

        $this->assertSame($account, $player->account);
    }

    #[TestDox('It has LovePoints')]
    public function test_it_has_love_points(): void
    {
        $lovePoints = LovePointsFixture::make();
        $player = PlayerFixture::make(lovePoints: $lovePoints);

        $this->assertSame($lovePoints, $player->lovePoints);
    }

    #[TestDox('It has MilliScore')]
    public function test_it_has_milli_score(): void
    {
        $milliScore = MilliScoreFixture::make();
        $player = PlayerFixture::make(milliScore: $milliScore);

        $this->assertSame($milliScore, $player->milliScore);
    }

    #[TestDox('It has CloudCoordinates')]
    public function test_it_has_cloud_coordinates(): void
    {
        $cloudCoordinates = CloudCoordinatesFixture::make();
        $player = PlayerFixture::make(cloudCoordinates: $cloudCoordinates);

        $this->assertSame($cloudCoordinates, $player->cloudCoordinates);
    }

    #[TestDox('It has UpgradableLevels')]
    public function test_it_has_upgradable_levels(): void
    {
        $upgradableLevels = UpgradableLevelsFixture::make();
        $player = PlayerFixture::make(upgradableLevels: $upgradableLevels);

        $this->assertSame($upgradableLevels, $player->upgradableLevels);
    }
}
