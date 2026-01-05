<?php

declare(strict_types=1);

namespace Bl\Domain\Upgradable;

enum UpgradableCategory: int
{
    case Organs = 0;
    case Bisous = 1;
    case Techniques = 2;

    /**
     * @return array<int, array<int, string>>
     */
    public static function toArray(): array
    {
        return [
            self::Organs->value => UpgradableOrgan::toArray(),
            self::Bisous->value => UpgradableBisou::toArray(),
            self::Techniques->value => UpgradableTechnique::toArray(),
        ];
    }

    /**
     * Returns all enum cases for this category.
     *
     * @return array<UpgradableOrgan|UpgradableBisou|UpgradableTechnique>
     */
    public function getCases(): array
    {
        return match ($this) {
            self::Organs => UpgradableOrgan::cases(),
            self::Bisous => UpgradableBisou::cases(),
            self::Techniques => UpgradableTechnique::cases(),
        };
    }

    /**
     * Returns the specific upgradable type enum for the given type index.
     */
    public function getType(int $type): UpgradableOrgan|UpgradableBisou|UpgradableTechnique
    {
        return match ($this) {
            self::Organs => UpgradableOrgan::from($type),
            self::Bisous => UpgradableBisou::from($type),
            self::Techniques => UpgradableTechnique::from($type),
        };
    }
}
