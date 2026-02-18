<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game\Player\UpgradableLevels;

use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;

/**
 * Why game rules live on the enum:
 *
 *   The primary growth axis is new upgradables (hair, wings, etc),
 *   not new computations. Keeping all rules per-case in one file
 *   makes adding a new upgradable a single-file change
 *   with exhaustive match safety.
 *
 *   Cost formulas and prerequisites will never depend on external factors
 *   (e.g. cloud position, bisou count, time-based events)
 *   and prerequisites will never become conditional/dynamic.
 *
 * Why Bisous live here too:
 *
 *   Organs and Techniques are true upgradables (levels, exponential cost),
 *   while Bisous are buildable quantities (flat cost, can be lost when blowing kisses).
 *   Despite this, the upgrade flow (check prerequisites, compute cost, increment by 1)
 *   is identical for all three categories.
 *
 *   Splitting Bisous into a separate Buildable enum would duplicate
 *   the prerequisite tree, dbColumn mapping, UpgradableLevels storage,
 *   and every layer that touches them
 *   (Application use cases, Domain service interfaces, Infrastructure DB implementations, UserInterface controllers/commands).
 *
 *   A shared abstraction wouldn't help either:
 *   the common logic IS the entire upgrade flow, leaving the sub-types as empty markers.
 *   The only behavioral difference (bisous lost when blowing kisses)
 *   belongs to the kiss-blowing domain, not here.
 *
 * @TODO add upgrade time calculation
 * @TODO remove database column once the schema has been translated from French to English
 *
 * @object-type ValueObject
 */
enum Upgradable: string
{
    // Organs
    case Heart = 'heart';
    case Mouth = 'mouth';
    case Tongue = 'tongue';
    case Teeth = 'teeth';
    case Legs = 'legs';
    case Eyes = 'eyes';

    // Bisous
    case Peck = 'peck';
    case Smooch = 'smooch';
    case FrenchKiss = 'french_kiss';

    // Techniques
    case HoldBreath = 'hold_breath';
    case Flirt = 'flirt';
    case Spit = 'spit';
    case Leap = 'leap';
    case Soup = 'soup';

    /**
     * @throws ValidationFailedException If $value isn't a valid upgradable
     */
    public static function fromString(string $value): self
    {
        $upgradable = self::tryFrom($value);
        if (null === $upgradable) {
            throw ValidationFailedException::make(
                "Invalid \"Upgradable\" parameter: it should be a valid upgradable name (`{$value}` given)",
            );
        }

        return $upgradable;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function dbColumn(): string
    {
        return match ($this) {
            // Organs
            self::Heart => 'coeur',
            self::Mouth => 'bouche',
            self::Tongue => 'langue',
            self::Teeth => 'dent',
            self::Legs => 'jambes',
            self::Eyes => 'oeil',
            // Bisous
            self::Peck => 'smack',
            self::Smooch => 'baiser',
            self::FrenchKiss => 'pelle',
            // Techniques
            self::HoldBreath => 'tech1',
            self::Flirt => 'tech2',
            self::Spit => 'tech3',
            self::Leap => 'tech4',
            self::Soup => 'soupe',
        };
    }

    /**
     * Cost is calculated as follow: ceil(base * exp(rate * level))
     * Each upgradable has its own base and rate.
     *
     * Bisous have a flat cost (e.g. 800, 3500, etc)
     *
     * @TODO (int) cast silently wraps when cost exceeds PHP_INT_MAX:
     *       - Leap (base 10000, rate 0.6) at level 58
     *       - Legs (base 1000, rate 0.6) at level 63
     *       - Soup (base 5000, rate 0.4) at level 88
     *       - Spit (base 3000, rate 0.4) at level 89
     *       - Flirt (base 2000, rate 0.4) at level 90
     *       - Heart (base 100, rate 0.4) at level 98
     *
     * @throws ServerErrorException If the cost formula overflows (level too high)
     */
    public function computeCost(UpgradableLevels $levels): int
    {
        $level = $levels->toInt($this);

        $cost = match ($this) {
            // Organs
            self::Heart => ceil(100 * exp(0.4 * $level)),
            self::Mouth => ceil(200 * exp(0.4 * $level)),
            self::Tongue => ceil(250 * exp(0.4 * $level)),
            self::Teeth => ceil(500 * exp(0.4 * $level)),
            self::Legs => ceil(1000 * exp(0.6 * $level)),
            self::Eyes => ceil(1000 * exp(0.4 * $level)),
            // Bisous
            self::Peck => 800,
            self::Smooch => 3500,
            self::FrenchKiss => 10000,
            // Techniques
            self::HoldBreath => ceil(1000 * exp(0.4 * $level)),
            self::Flirt => ceil(2000 * exp(0.4 * $level)),
            self::Spit => ceil(3000 * exp(0.4 * $level)),
            self::Leap => ceil(10000 * exp(0.6 * $level)),
            self::Soup => ceil(5000 * exp(0.4 * $level)),
        };

        if (\is_float($cost) && is_infinite($cost)) {
            throw ServerErrorException::make(
                "Invalid \"Upgradable\" cost: the exponential formula produces infinity (`{$this->value}` at level `{$level}` given)",
            );
        }

        return (int) $cost;
    }

    /**
     * @return list<array{self, int}>
     */
    public function getPrerequisites(): array
    {
        return match ($this) {
            // Organs
            self::Heart => [],
            self::Mouth => [[self::Heart, 2]],
            self::Tongue => [[self::Mouth, 2], [self::Heart, 5]],
            self::Teeth => [[self::Mouth, 2]],
            self::Legs => [[self::Heart, 15]],
            self::Eyes => [[self::Heart, 10]],
            // Bisous
            self::Peck => [[self::Mouth, 2]],
            self::Smooch => [[self::Mouth, 6]],
            self::FrenchKiss => [[self::Tongue, 5], [self::Mouth, 10]],
            // Techniques
            self::HoldBreath => [[self::Heart, 3], [self::Mouth, 2]],
            self::Flirt => [[self::Heart, 5], [self::Mouth, 4]],
            self::Spit => [[self::HoldBreath, 1], [self::Flirt, 3], [self::Tongue, 3]],
            self::Leap => [[self::Legs, 2]],
            self::Soup => [[self::Heart, 15], [self::Mouth, 8], [self::Tongue, 4]],
        };
    }

    /**
     * @throws ValidationFailedException If the upgradable isn't unlocked yet (e.g. legs require heart >= 15)
     */
    public function checkPrerequisites(UpgradableLevels $levels): void
    {
        foreach ($this->getPrerequisites() as [$required, $minLevel]) {
            if ($levels->toInt($required) < $minLevel) {
                throw ValidationFailedException::make(
                    "Invalid \"Upgradable\" parameter: prerequisites not met for {$this->value} ({$required->value} >= {$minLevel} required, got {$levels->toInt($required)})",
                );
            }
        }
    }
}
