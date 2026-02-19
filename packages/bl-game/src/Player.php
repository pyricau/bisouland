<?php

declare(strict_types=1);

namespace Bl\Game;

use Bl\Auth\Account;
use Bl\Game\Player\CloudCoordinates;
use Bl\Game\Player\LovePoints;
use Bl\Game\Player\MilliScore;
use Bl\Game\Player\UpgradableLevels;

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
