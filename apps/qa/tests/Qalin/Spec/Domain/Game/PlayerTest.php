<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game;

use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Tests\Fixtures\Domain\Auth\AccountFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\CloudCoordinatesFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\LovePointsFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\ScoreFixture;
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

    #[TestDox('It has Score')]
    public function test_it_has_score(): void
    {
        $score = ScoreFixture::make();
        $player = PlayerFixture::make(score: $score);

        $this->assertSame($score, $player->score);
    }

    #[TestDox('It has CloudCoordinates')]
    public function test_it_has_cloud_coordinates(): void
    {
        $cloudCoordinates = CloudCoordinatesFixture::make();
        $player = PlayerFixture::make(cloudCoordinates: $cloudCoordinates);

        $this->assertSame($cloudCoordinates, $player->cloudCoordinates);
    }
}
