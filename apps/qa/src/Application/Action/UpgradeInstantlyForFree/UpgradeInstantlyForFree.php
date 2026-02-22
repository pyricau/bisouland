<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\UpgradeInstantlyForFree;

/**
 * @object-type DataTransferObject
 */
final readonly class UpgradeInstantlyForFree
{
    public function __construct(
        public string $username,
        public string $upgradable,
        public int $levels = 1,
    ) {
    }
}
