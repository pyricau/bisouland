<?php

declare(strict_types=1);

namespace Bl\Domain\Upgradable;

enum UpgradableBisou: int
{
    case Peck = 0;
    case Smooch = 1;
    case FrenchKiss = 2;

    /**
     * @return array<int, string>
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $bisou): string => $bisou->toString(),
            self::cases(),
        );
    }

    /**
     * Returns the database column name.
     */
    public function toString(): string
    {
        return match ($this) {
            self::Peck => 'smack',
            self::Smooch => 'baiser',
            self::FrenchKiss => 'pelle', // aka baiser langoureux
        };
    }
}
