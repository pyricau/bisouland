<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

use Bl\Auth\Account;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Domain\Game\Player\MilliScore;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;

/**
 * @object-type Entity
 */
final readonly class Player
{
    public function __construct(
        public Account $account,
        public LovePoints $lovePoints,
        public MilliScore $milliScore,
        public CloudCoordinates $cloudCoordinates,
        public UpgradableLevels $upgradableLevels,
    ) {
    }
}
