<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Fixtures\Domain\Game;

use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Domain\Game\Player\MilliScore;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Tests\Fixtures\Domain\Auth\AccountFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\CloudCoordinatesFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\LovePointsFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\MilliScoreFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevelsFixture;

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
