<?php

declare(strict_types=1);

namespace Bl\Domain\Upgradable;

enum UpgradableOrgan: int
{
    case Heart = 0;
    case Mouth = 1;
    case Tongue = 2;
    case Teeth = 3;
    case Legs = 4;
    case Eyes = 5;

    /**
     * @return array<int, string>
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $organ): string => $organ->toString(),
            self::cases(),
        );
    }

    /**
     * Returns the database column name.
     */
    public function toString(): string
    {
        return match ($this) {
            self::Heart => 'coeur',
            self::Mouth => 'bouche',
            self::Tongue => 'langue',
            self::Teeth => 'dent',
            self::Legs => 'jambes',
            self::Eyes => 'oeil',
        };
    }
}
