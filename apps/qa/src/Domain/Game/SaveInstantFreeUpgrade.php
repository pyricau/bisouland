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
 * Saves an upgrade for a player's upgradable, instantly and for free:
 * - Enforces the full prerequisite tree
 * - Computes the cost that would have been paid and adds it to the player's score
 * - Increments the upgradable's level by 1
 * - Does NOT remove LovePoints from the player
 * - Does NOT queue the upgrade (no evolution/liste entry)
 */
interface SaveInstantFreeUpgrade
{
    /**
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ValidationFailedException If the upgradable isn't unlocked yet (e.g. legs require heart >= 15)
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function save(Username $username, Upgradable $upgradable): Player;
}
