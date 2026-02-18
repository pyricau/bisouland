<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player\UpgradableLevels\Upgradable;

use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Upgradable::class)]
#[Small]
final class DbColumnTest extends TestCase
{
    #[TestDox('It maps $upgradable to db column $expectedColumn')]
    #[DataProvider('dbColumnProvider')]
    public function test_it_maps_to_db_column(
        Upgradable $upgradable,
        string $expectedColumn,
    ): void {
        $this->assertSame($expectedColumn, $upgradable->dbColumn());
    }

    /**
     * @return \Iterator<array{upgradable: Upgradable, expectedColumn: string}>
     */
    public static function dbColumnProvider(): \Iterator
    {
        yield ['upgradable' => Upgradable::Heart, 'expectedColumn' => 'coeur'];
        yield ['upgradable' => Upgradable::Mouth, 'expectedColumn' => 'bouche'];
        yield ['upgradable' => Upgradable::Tongue, 'expectedColumn' => 'langue'];
        yield ['upgradable' => Upgradable::Teeth, 'expectedColumn' => 'dent'];
        yield ['upgradable' => Upgradable::Legs, 'expectedColumn' => 'jambes'];
        yield ['upgradable' => Upgradable::Eyes, 'expectedColumn' => 'oeil'];
        yield ['upgradable' => Upgradable::Peck, 'expectedColumn' => 'smack'];
        yield ['upgradable' => Upgradable::Smooch, 'expectedColumn' => 'baiser'];
        yield ['upgradable' => Upgradable::FrenchKiss, 'expectedColumn' => 'pelle'];
        yield ['upgradable' => Upgradable::HoldBreath, 'expectedColumn' => 'tech1'];
        yield ['upgradable' => Upgradable::Flirt, 'expectedColumn' => 'tech2'];
        yield ['upgradable' => Upgradable::Spit, 'expectedColumn' => 'tech3'];
        yield ['upgradable' => Upgradable::Leap, 'expectedColumn' => 'tech4'];
        yield ['upgradable' => Upgradable::Soup, 'expectedColumn' => 'soupe'];
    }
}
