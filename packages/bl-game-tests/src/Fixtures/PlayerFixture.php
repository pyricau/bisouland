<?php

declare(strict_types=1);

namespace Bl\Game\Tests\Fixtures;

use Bl\Auth\Account;
use Bl\Auth\Tests\Fixtures\AccountFixture;
use Bl\Game\Player;
use Bl\Game\Player\CloudCoordinates;
use Bl\Game\Player\LovePoints;
use Bl\Game\Player\MilliScore;
use Bl\Game\Player\UpgradableLevels;
use Bl\Game\Tests\Fixtures\Player\CloudCoordinatesFixture;
use Bl\Game\Tests\Fixtures\Player\LovePointsFixture;
use Bl\Game\Tests\Fixtures\Player\MilliScoreFixture;
use Bl\Game\Tests\Fixtures\Player\UpgradableLevelsFixture;

final readonly class PlayerFixture
{
    public static function make(
        ?Account $account = null,
        ?LovePoints $lovePoints = null,
        ?MilliScore $milliScore = null,
        ?CloudCoordinates $cloudCoordinates = null,
        ?UpgradableLevels $upgradableLevels = null,
    ): Player {
        return new Player(
            account: $account ?? AccountFixture::make(),
            lovePoints: $lovePoints ?? LovePointsFixture::make(),
            milliScore: $milliScore ?? MilliScoreFixture::make(),
            cloudCoordinates: $cloudCoordinates ?? CloudCoordinatesFixture::make(),
            upgradableLevels: $upgradableLevels ?? UpgradableLevelsFixture::make(),
        );
    }
}
