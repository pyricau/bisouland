<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevelsFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpgradableLevels::class)]
#[Small]
final class UpgradableLevelsTest extends TestCase
{
    #[TestDox('It can be converted from/to ints')]
    public function test_it_can_be_converted_from_and_to_ints(): void
    {
        /**
         * @var array{
         *     heart: int, mouth: int,
         *     tongue: int, teeth: int, legs: int, eyes: int,
         *     peck: int, smooch: int, frenchKiss: int,
         *     holdBreath: int, flirt: int, spit: int, leap: int, soup: int,
         * } $values
         */
        $values = [
            'heart' => 5, 'mouth' => 3,
            'tongue' => 2, 'teeth' => 4, 'legs' => 1, 'eyes' => 3,
            'peck' => 7, 'smooch' => 2, 'frenchKiss' => 1,
            'holdBreath' => 5, 'flirt' => 3, 'spit' => 2, 'leap' => 4, 'soup' => 1,
        ];
        $upgradableLevels = UpgradableLevelsFixture::make(...$values);

        $this->assertInstanceOf(UpgradableLevels::class, $upgradableLevels);
        foreach (Upgradable::cases() as $upgradable) {
            $this->assertSame($values[lcfirst($upgradable->name)], $upgradableLevels->toInt($upgradable));
        }
    }

    /**
     * @param array<string, int|string> $row
     */
    #[TestDox('It can be converted from/to array ($scenario)')]
    #[DataProvider('fromArrayProvider')]
    public function test_it_can_be_converted_from_and_to_array(
        string $scenario,
        array $row,
    ): void {
        $upgradableLevels = UpgradableLevels::fromArray($row);

        foreach (Upgradable::cases() as $upgradable) {
            $expected = isset($row[$upgradable->value])
                ? (int) $row[$upgradable->value]
                : UpgradableLevelsFixture::make()->toInt($upgradable);
            $this->assertSame(
                $expected,
                $upgradableLevels->toInt($upgradable),
            );
        }
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     row: array<string, int|string>,
     * }>
     */
    public static function fromArrayProvider(): \Iterator
    {
        yield [
            'scenario' => 'int values',
            'row' => [
                'heart' => 5, 'mouth' => 3,
                'tongue' => 2, 'teeth' => 4, 'legs' => 1, 'eyes' => 3,
                'peck' => 7, 'smooch' => 2, 'french_kiss' => 1,
                'hold_breath' => 5, 'flirt' => 3, 'spit' => 2, 'leap' => 4, 'soup' => 1,
            ],
        ];
        yield [
            'scenario' => 'string numeric values',
            'row' => [
                'heart' => '5', 'mouth' => '3',
                'tongue' => '2', 'teeth' => '4', 'legs' => '1', 'eyes' => '3',
                'peck' => '7', 'smooch' => '2', 'french_kiss' => '1',
                'hold_breath' => '5', 'flirt' => '3', 'spit' => '2', 'leap' => '4', 'soup' => '1',
            ],
        ];
        yield [
            'scenario' => 'missing keys fall back to starting levels',
            'row' => [
                'heart' => 1, 'mouth' => 1,
            ],
        ];
    }

    /**
     * @param array<string, int|string> $row
     */
    #[TestDox('It fails when array item $upgradable is not numeric')]
    #[DataProvider('invalidArrayProvider')]
    public function test_it_fails_when_array_item_is_not_numeric(
        Upgradable $upgradable,
        array $row,
    ): void {
        $this->expectException(ValidationFailedException::class);

        UpgradableLevels::fromArray($row);
    }

    /**
     * @return \Iterator<array{
     *     upgradable: Upgradable,
     *     row: array<string, int|string>,
     * }>
     */
    public static function invalidArrayProvider(): \Iterator
    {
        foreach (Upgradable::cases() as $upgradable) {
            yield [
                'upgradable' => $upgradable,
                'row' => [$upgradable->value => 'not a numeric'],
            ];
        }
    }

    /**
     * @param array<string, string> $row
     */
    #[TestDox('It fails when array item is a $scenario')]
    #[DataProvider('nonIntegerNumericProvider')]
    public function test_it_fails_when_array_item_is_non_integer_numeric(
        string $scenario,
        array $row,
    ): void {
        $this->expectException(ValidationFailedException::class);

        UpgradableLevels::fromArray($row);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     row: array<string, string>,
     * }>
     */
    public static function nonIntegerNumericProvider(): \Iterator
    {
        yield [
            'scenario' => 'decimal string ("1.9")',
            'row' => ['heart' => '1.9'],
        ];
        yield [
            'scenario' => 'scientific notation string ("1e3")',
            'row' => ['heart' => '1e3'],
        ];
    }

    #[TestDox('It has $upgradable level starting at $expectedValue')]
    #[DataProvider('defaultProvider')]
    public function test_it_has_starting_levels(
        Upgradable $upgradable,
        int $expectedValue,
    ): void {
        $upgradableLevels = UpgradableLevelsFixture::make();

        $this->assertSame($expectedValue, $upgradableLevels->toInt($upgradable));
    }

    /**
     * @return \Iterator<array{
     *     upgradable: Upgradable,
     *     expectedValue: int,
     * }>
     */
    public static function defaultProvider(): \Iterator
    {
        foreach (Upgradable::cases() as $upgradable) {
            yield [
                'upgradable' => $upgradable,
                'expectedValue' => UpgradableLevels::STARTING_LEVELS[$upgradable->value],
            ];
        }
    }

    /**
     * @param array<string, int> $row
     */
    #[TestDox('It fails when raw $scenario')]
    #[DataProvider('invalidLevelProvider')]
    public function test_it_fails_when_raw_level_is_too_low(
        string $scenario,
        array $row,
    ): void {
        $this->expectException(ValidationFailedException::class);

        UpgradableLevels::fromArray($row);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     row: array<string, int>,
     * }>
     */
    public static function invalidLevelProvider(): \Iterator
    {
        foreach (UpgradableLevels::STARTING_LEVELS as $name => $min) {
            yield [
                'scenario' => "{$name} is too low (< {$min})",
                'row' => [...UpgradableLevels::STARTING_LEVELS, $name => $min - 1],
            ];
        }
    }
}
