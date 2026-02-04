<?php

declare(strict_types=1);

namespace Bl\Domain\Upgradable;

enum UpgradableTechnique: int
{
    case HoldBreath = 0;
    case Flirt = 1;
    case Spit = 2;
    case Leap = 3;
    case Soup = 4;

    /**
     * @return array<int, string>
     */
    public static function toArray(): array
    {
        return array_map(
            static fn (self $technique): string => $technique->toString(),
            self::cases(),
        );
    }

    /**
     * Returns the database column name.
     */
    public function toString(): string
    {
        return match ($this) {
            self::HoldBreath => 'tech1', // aka Apnee
            self::Flirt => 'tech2',
            self::Spit => 'tech3', // aka Crachat
            self::Leap => 'tech4', // aka Bond
            self::Soup => 'soupe',
        };
    }
}
