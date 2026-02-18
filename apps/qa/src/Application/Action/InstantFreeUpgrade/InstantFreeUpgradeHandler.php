<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\InstantFreeUpgrade;

use Bl\Auth\Account\Username;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Game\ApplyCompletedUpgrade;
use Bl\Game\FindPlayer;
use Bl\Game\Player\UpgradableLevels\Upgradable;

/**
 * @object-type UseCase
 */
final readonly class InstantFreeUpgradeHandler
{
    public function __construct(
        private ApplyCompletedUpgrade $applyCompletedUpgrade,
        private FindPlayer $findPlayer,
    ) {
    }

    /**
     * @throws ValidationFailedException If the username is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the username is not an already existing one
     * @throws ValidationFailedException If the upgradable is not a valid upgradable name
     * @throws ValidationFailedException If the levels is < 1
     * @throws ValidationFailedException If the upgradable isn't unlocked yet (e.g. legs require heart >= 15)
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(InstantFreeUpgrade $input): InstantFreeUpgradeOutput
    {
        $username = Username::fromString($input->username);
        $upgradable = Upgradable::fromString($input->upgradable);
        if ($input->levels < 1) {
            throw ValidationFailedException::make(
                "Invalid \"InstantFreeUpgrade\" parameter: it should have levels >= 1 (`{$input->levels}` given)",
            );
        }

        $player = $this->findPlayer->find($username);

        for ($i = 0; $i < $input->levels; ++$i) {
            $upgradable->checkPrerequisites($player->upgradableLevels);
            $milliScore = $upgradable->computeCost($player->upgradableLevels);
            $player = $this->applyCompletedUpgrade->apply($username, $upgradable, $milliScore);
        }

        return new InstantFreeUpgradeOutput($player);
    }
}
