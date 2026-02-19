<?php

declare(strict_types=1);

namespace Bl\Game\Player;

use Bl\Exception\ValidationFailedException;
use Bl\Game\Player\UpgradableLevels\Upgradable;

/**
 * @object-type ValueObject
 */
final readonly class UpgradableLevels
{
    public const array STARTING_LEVELS = [
        'heart' => 1, 'mouth' => 1,
        'tongue' => 0, 'teeth' => 0, 'legs' => 0, 'eyes' => 0,
        'peck' => 0, 'smooch' => 0, 'french_kiss' => 0,
        'hold_breath' => 0, 'flirt' => 0, 'spit' => 0, 'leap' => 0, 'soup' => 0,
    ];

    private function __construct(
        private int $heart,
        private int $mouth,
        private int $tongue,
        private int $teeth,
        private int $legs,
        private int $eyes,
        private int $peck,
        private int $smooch,
        private int $frenchKiss,
        private int $holdBreath,
        private int $flirt,
        private int $spit,
        private int $leap,
        private int $soup,
    ) {
    }

    /**
     * @throws ValidationFailedException If $heart is < 1
     * @throws ValidationFailedException If $mouth is < 1
     * @throws ValidationFailedException If $tongue is < 0
     * @throws ValidationFailedException If $teeth is < 0
     * @throws ValidationFailedException If $legs is < 0
     * @throws ValidationFailedException If $eyes is < 0
     * @throws ValidationFailedException If $peck is < 0
     * @throws ValidationFailedException If $smooch is < 0
     * @throws ValidationFailedException If $frenchKiss is < 0
     * @throws ValidationFailedException If $holdBreath is < 0
     * @throws ValidationFailedException If $flirt is < 0
     * @throws ValidationFailedException If $spit is < 0
     * @throws ValidationFailedException If $leap is < 0
     * @throws ValidationFailedException If $soup is < 0
     */
    public static function fromInts(
        int $heart,
        int $mouth,
        int $tongue,
        int $teeth,
        int $legs,
        int $eyes,
        int $peck,
        int $smooch,
        int $frenchKiss,
        int $holdBreath,
        int $flirt,
        int $spit,
        int $leap,
        int $soup,
    ): self {
        $invalid = match (true) {
            $heart < 1 => "heart should be >= 1 (`{$heart}` given)",
            $mouth < 1 => "mouth should be >= 1 (`{$mouth}` given)",
            $tongue < 0 => "tongue should be >= 0 (`{$tongue}` given)",
            $teeth < 0 => "teeth should be >= 0 (`{$teeth}` given)",
            $legs < 0 => "legs should be >= 0 (`{$legs}` given)",
            $eyes < 0 => "eyes should be >= 0 (`{$eyes}` given)",
            $peck < 0 => "peck should be >= 0 (`{$peck}` given)",
            $smooch < 0 => "smooch should be >= 0 (`{$smooch}` given)",
            $frenchKiss < 0 => "frenchKiss should be >= 0 (`{$frenchKiss}` given)",
            $holdBreath < 0 => "holdBreath should be >= 0 (`{$holdBreath}` given)",
            $flirt < 0 => "flirt should be >= 0 (`{$flirt}` given)",
            $spit < 0 => "spit should be >= 0 (`{$spit}` given)",
            $leap < 0 => "leap should be >= 0 (`{$leap}` given)",
            $soup < 0 => "soup should be >= 0 (`{$soup}` given)",
            default => '',
        };

        if ('' !== $invalid) {
            throw ValidationFailedException::make(
                "Invalid \"UpgradableLevels\" parameter: {$invalid}",
            );
        }

        return new self(
            $heart,
            $mouth,
            $tongue,
            $teeth,
            $legs,
            $eyes,
            $peck,
            $smooch,
            $frenchKiss,
            $holdBreath,
            $flirt,
            $spit,
            $leap,
            $soup,
        );
    }

    /**
     * @param array<string, int|string> $row keyed by Upgradable::value (snake_case)
     *
     * @throws ValidationFailedException If a value is not numeric
     * @throws ValidationFailedException If a level is below its minimum
     */
    public static function fromArray(array $row): self
    {
        $values = [];
        foreach (Upgradable::cases() as $upgradable) {
            $key = $upgradable->value;
            $raw = $row[$key] ?? self::STARTING_LEVELS[$key];
            if (!\is_int($raw) && !ctype_digit($raw)) {
                throw ValidationFailedException::make(
                    "Invalid \"UpgradableLevels\" parameter: {$key} should be an integer (`{$raw}` given)",
                );
            }

            $values[$key] = (int) $raw;
        }

        return self::fromInts(
            heart: $values['heart'],
            mouth: $values['mouth'],
            tongue: $values['tongue'],
            teeth: $values['teeth'],
            legs: $values['legs'],
            eyes: $values['eyes'],
            peck: $values['peck'],
            smooch: $values['smooch'],
            frenchKiss: $values['french_kiss'],
            holdBreath: $values['hold_breath'],
            flirt: $values['flirt'],
            spit: $values['spit'],
            leap: $values['leap'],
            soup: $values['soup'],
        );
    }

    public static function create(): self
    {
        return self::fromArray(self::STARTING_LEVELS);
    }

    public function toInt(Upgradable $upgradable): int
    {
        return match ($upgradable) {
            Upgradable::Heart => $this->heart,
            Upgradable::Mouth => $this->mouth,
            Upgradable::Tongue => $this->tongue,
            Upgradable::Teeth => $this->teeth,
            Upgradable::Legs => $this->legs,
            Upgradable::Eyes => $this->eyes,
            Upgradable::Peck => $this->peck,
            Upgradable::Smooch => $this->smooch,
            Upgradable::FrenchKiss => $this->frenchKiss,
            Upgradable::HoldBreath => $this->holdBreath,
            Upgradable::Flirt => $this->flirt,
            Upgradable::Spit => $this->spit,
            Upgradable::Leap => $this->leap,
            Upgradable::Soup => $this->soup,
        };
    }
}
