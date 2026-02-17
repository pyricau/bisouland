<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;

/**
 * @object-type Service
 *
 * Applies a single completed upgrade for a player's upgradable:
 * - Increments the upgradable's level by 1
 * - Adds the pre-computed score
 * - Does NOT check prerequisites (caller's responsibility)
 * - Does NOT remove LovePoints from the player
 */
interface ApplyCompletedUpgrade
{
    /**
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function apply(Username $username, Upgradable $upgradable, int $score): Player;
}
