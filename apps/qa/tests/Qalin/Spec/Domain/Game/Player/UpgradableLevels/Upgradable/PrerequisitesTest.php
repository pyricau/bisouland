<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player\UpgradableLevels\Upgradable;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevelsFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Upgradable::class)]
#[Small]
final class PrerequisitesTest extends TestCase
{
    /**
     * @param list<array{Upgradable, int}> $expectedPrerequisites
     */
    #[TestDox('It gets prerequisites for $scenario')]
    #[DataProvider('getPrerequisitesProvider')]
    public function test_it_gets_prerequisites(
        string $scenario,
        Upgradable $upgradable,
        array $expectedPrerequisites,
    ): void {
        $this->assertSame($expectedPrerequisites, $upgradable->getPrerequisites());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     expectedPrerequisites: list<array{Upgradable, int}>,
     * }>
     */
    public static function getPrerequisitesProvider(): \Iterator
    {
        yield [
            'scenario' => 'heart: no prerequisites',
            'upgradable' => Upgradable::Heart,
            'expectedPrerequisites' => [],
        ];
        yield [
            'scenario' => 'mouth: requires heart >= 2',
            'upgradable' => Upgradable::Mouth,
            'expectedPrerequisites' => [[Upgradable::Heart, 2]],
        ];
        yield [
            'scenario' => 'tongue: requires mouth >= 2 and heart >= 5',
            'upgradable' => Upgradable::Tongue,
            'expectedPrerequisites' => [[Upgradable::Mouth, 2], [Upgradable::Heart, 5]],
        ];
        yield [
            'scenario' => 'teeth: requires mouth >= 2',
            'upgradable' => Upgradable::Teeth,
            'expectedPrerequisites' => [[Upgradable::Mouth, 2]],
        ];
        yield [
            'scenario' => 'legs: requires heart >= 15',
            'upgradable' => Upgradable::Legs,
            'expectedPrerequisites' => [[Upgradable::Heart, 15]],
        ];
        yield [
            'scenario' => 'eyes: requires heart >= 10',
            'upgradable' => Upgradable::Eyes,
            'expectedPrerequisites' => [[Upgradable::Heart, 10]],
        ];
        yield [
            'scenario' => 'peck: requires mouth >= 2',
            'upgradable' => Upgradable::Peck,
            'expectedPrerequisites' => [[Upgradable::Mouth, 2]],
        ];
        yield [
            'scenario' => 'smooch: requires mouth >= 6',
            'upgradable' => Upgradable::Smooch,
            'expectedPrerequisites' => [[Upgradable::Mouth, 6]],
        ];
        yield [
            'scenario' => 'french_kiss: requires tongue >= 5 and mouth >= 10',
            'upgradable' => Upgradable::FrenchKiss,
            'expectedPrerequisites' => [[Upgradable::Tongue, 5], [Upgradable::Mouth, 10]],
        ];
        yield [
            'scenario' => 'hold_breath: requires heart >= 3 and mouth >= 2',
            'upgradable' => Upgradable::HoldBreath,
            'expectedPrerequisites' => [[Upgradable::Heart, 3], [Upgradable::Mouth, 2]],
        ];
        yield [
            'scenario' => 'flirt: requires heart >= 5 and mouth >= 4',
            'upgradable' => Upgradable::Flirt,
            'expectedPrerequisites' => [[Upgradable::Heart, 5], [Upgradable::Mouth, 4]],
        ];
        yield [
            'scenario' => 'spit: requires hold_breath >= 1, flirt >= 3 and tongue >= 3',
            'upgradable' => Upgradable::Spit,
            'expectedPrerequisites' => [[Upgradable::HoldBreath, 1], [Upgradable::Flirt, 3], [Upgradable::Tongue, 3]],
        ];
        yield [
            'scenario' => 'leap: requires legs >= 2',
            'upgradable' => Upgradable::Leap,
            'expectedPrerequisites' => [[Upgradable::Legs, 2]],
        ];
        yield [
            'scenario' => 'soup: requires heart >= 15, mouth >= 8 and tongue >= 4',
            'upgradable' => Upgradable::Soup,
            'expectedPrerequisites' => [[Upgradable::Heart, 15], [Upgradable::Mouth, 8], [Upgradable::Tongue, 4]],
        ];
    }

    #[TestDox('It passes prerequisites check for $scenario')]
    #[DataProvider('prerequisitesMetProvider')]
    public function test_it_passes_prerequisites_check_when_met(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
    ): void {
        $upgradable->checkPrerequisites($levels);

        $this->addToAssertionCount(1);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     * }>
     */
    public static function prerequisitesMetProvider(): \Iterator
    {
        foreach (Upgradable::cases() as $upgradable) {
            $prerequisites = $upgradable->getPrerequisites();
            $metLevels = self::buildMetLevels($prerequisites);
            $parts = array_map(static fn (array $r): string => "{$r[0]->value} >= {$r[1]}", $prerequisites);
            $scenario = [] !== $prerequisites
                ? "{$upgradable->value}: requires ".implode(' and ', $parts)
                : "{$upgradable->value}: no prerequisites";

            yield [
                'scenario' => $scenario,
                'upgradable' => $upgradable,
                'levels' => UpgradableLevelsFixture::make(...$metLevels),
            ];
        }
    }

    #[TestDox('It fails prerequisites check for $scenario')]
    #[DataProvider('prerequisitesNotMetProvider')]
    public function test_it_fails_prerequisites_check_when_not_met(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
    ): void {
        $this->expectException(ValidationFailedException::class);

        $upgradable->checkPrerequisites($levels);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     * }>
     */
    public static function prerequisitesNotMetProvider(): \Iterator
    {
        foreach (Upgradable::cases() as $upgradable) {
            $prerequisites = $upgradable->getPrerequisites();
            if ([] === $prerequisites) {
                continue;
            }

            $metLevels = self::buildMetLevels($prerequisites);
            foreach ($prerequisites as [$required, $minLevel]) {
                $failLevels = $metLevels;
                $failLevels[lcfirst($required->name)] = $minLevel - 1;

                yield [
                    'scenario' => "{$upgradable->value} when {$required->value} < {$minLevel}",
                    'upgradable' => $upgradable,
                    'levels' => UpgradableLevelsFixture::make(...$failLevels),
                ];
            }
        }
    }

    /**
     * @param list<array{Upgradable, int}> $prerequisites
     *
     * @return array<string, int>
     */
    private static function buildMetLevels(array $prerequisites): array
    {
        $levels = [];
        foreach ($prerequisites as [$required, $minLevel]) {
            $levels[lcfirst($required->name)] = $minLevel;
        }

        return $levels;
    }
}
