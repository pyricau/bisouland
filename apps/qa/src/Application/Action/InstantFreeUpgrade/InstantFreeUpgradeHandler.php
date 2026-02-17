<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\InstantFreeUpgrade;

use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type UseCase
 */
final readonly class InstantFreeUpgradeHandler
{
    public function __construct(
        // TODO: inject domain service dependencies
    ) {
    }

    /**
     * @throws ValidationFailedException If a parameter is invalid
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(InstantFreeUpgrade $input): InstantFreeUpgradeOutput
    {
        // TODO: implement domain logic, return new InstantFreeUpgradeOutput($player)
    }
}
