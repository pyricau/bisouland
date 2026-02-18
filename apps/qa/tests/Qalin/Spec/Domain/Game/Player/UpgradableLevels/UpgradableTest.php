<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player\UpgradableLevels;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Upgradable::class)]
#[Small]
final class UpgradableTest extends TestCase
{
    #[TestDox('It can be created from string $value')]
    #[DataProvider('validValuesProvider')]
    public function test_it_can_be_created_from_string(
        string $value,
        Upgradable $expected,
    ): void {
        $this->assertSame($expected, Upgradable::fromString($value));
    }

    /**
     * @return \Iterator<array{value: string, expected: Upgradable}>
     */
    public static function validValuesProvider(): \Iterator
    {
        foreach (Upgradable::cases() as $upgradable) {
            yield ['value' => $upgradable->value, 'expected' => $upgradable];
        }
    }

    #[TestDox('It fails on invalid value (i.e. value not in enum)')]
    public function test_it_fails_on_invalid_value(): void
    {
        $this->expectException(ValidationFailedException::class);

        Upgradable::fromString('invalid');
    }
}
