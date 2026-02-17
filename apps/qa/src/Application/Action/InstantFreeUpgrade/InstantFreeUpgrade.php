<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\InstantFreeUpgrade;

/**
 * @object-type DataTransferObject
 */
final readonly class InstantFreeUpgrade
{
    public function __construct(
        public string $username,
        public string $upgradable,
        public int $levels,
    ) {
    }
}
