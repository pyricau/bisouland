<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player\UpgradableLevels\Upgradable;

use Bl\Qa\Domain\Exception\ServerErrorException;
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
final class CostTest extends TestCase
{
    #[TestDox('It computes exponential cost for organ $scenario')]
    #[DataProvider('organCostProvider')]
    public function test_it_computes_exponential_cost_for_organs(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
        int $expectedCost,
    ): void {
        $this->assertSame($expectedCost, $upgradable->computeCost($levels));
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     *     expectedCost: int,
     * }>
     */
    public static function organCostProvider(): \Iterator
    {
        // Formula: ceil(base * exp(rate * level))

        yield [
            'scenario' => 'heart at level 2: ceil(100 * exp(0.4 * 2)) = 223',
            'upgradable' => Upgradable::Heart,
            'levels' => UpgradableLevelsFixture::make(heart: 2),
            'expectedCost' => 223,
        ];
        yield [
            'scenario' => 'heart at level 3: ceil(100 * exp(0.4 * 3)) = 333',
            'upgradable' => Upgradable::Heart,
            'levels' => UpgradableLevelsFixture::make(heart: 3),
            'expectedCost' => 333,
        ];
        yield [
            'scenario' => 'heart at level 4: ceil(100 * exp(0.4 * 4)) = 496',
            'upgradable' => Upgradable::Heart,
            'levels' => UpgradableLevelsFixture::make(heart: 4),
            'expectedCost' => 496,
        ];
        yield [
            'scenario' => 'heart at level 42: ceil(100 * exp(0.4 * 42)) = 1,977,640,266',
            'upgradable' => Upgradable::Heart,
            'levels' => UpgradableLevelsFixture::make(heart: 42),
            'expectedCost' => 1_977_640_266,
        ];

        yield [
            'scenario' => 'mouth at level 2: ceil(200 * exp(0.4 * 2)) = 446',
            'upgradable' => Upgradable::Mouth,
            'levels' => UpgradableLevelsFixture::make(mouth: 2),
            'expectedCost' => 446,
        ];
        yield [
            'scenario' => 'mouth at level 3: ceil(200 * exp(0.4 * 3)) = 665',
            'upgradable' => Upgradable::Mouth,
            'levels' => UpgradableLevelsFixture::make(mouth: 3),
            'expectedCost' => 665,
        ];
        yield [
            'scenario' => 'mouth at level 4: ceil(200 * exp(0.4 * 4)) = 991',
            'upgradable' => Upgradable::Mouth,
            'levels' => UpgradableLevelsFixture::make(mouth: 4),
            'expectedCost' => 991,
        ];
        yield [
            'scenario' => 'mouth at level 42: ceil(200 * exp(0.4 * 42)) = 3,955,280,532',
            'upgradable' => Upgradable::Mouth,
            'levels' => UpgradableLevelsFixture::make(mouth: 42),
            'expectedCost' => 3_955_280_532,
        ];

        yield [
            'scenario' => 'tongue at level 1: ceil(250 * exp(0.4 * 1)) = 373',
            'upgradable' => Upgradable::Tongue,
            'levels' => UpgradableLevelsFixture::make(tongue: 1),
            'expectedCost' => 373,
        ];
        yield [
            'scenario' => 'tongue at level 2: ceil(250 * exp(0.4 * 2)) = 557',
            'upgradable' => Upgradable::Tongue,
            'levels' => UpgradableLevelsFixture::make(tongue: 2),
            'expectedCost' => 557,
        ];
        yield [
            'scenario' => 'tongue at level 3: ceil(250 * exp(0.4 * 3)) = 831',
            'upgradable' => Upgradable::Tongue,
            'levels' => UpgradableLevelsFixture::make(tongue: 3),
            'expectedCost' => 831,
        ];
        yield [
            'scenario' => 'tongue at level 42: ceil(250 * exp(0.4 * 42)) = 4,944,100,665',
            'upgradable' => Upgradable::Tongue,
            'levels' => UpgradableLevelsFixture::make(tongue: 42),
            'expectedCost' => 4_944_100_665,
        ];

        yield [
            'scenario' => 'teeth at level 1: ceil(500 * exp(0.4 * 1)) = 746',
            'upgradable' => Upgradable::Teeth,
            'levels' => UpgradableLevelsFixture::make(teeth: 1),
            'expectedCost' => 746,
        ];
        yield [
            'scenario' => 'teeth at level 2: ceil(500 * exp(0.4 * 2)) = 1,113',
            'upgradable' => Upgradable::Teeth,
            'levels' => UpgradableLevelsFixture::make(teeth: 2),
            'expectedCost' => 1_113,
        ];
        yield [
            'scenario' => 'teeth at level 3: ceil(500 * exp(0.4 * 3)) = 1,661',
            'upgradable' => Upgradable::Teeth,
            'levels' => UpgradableLevelsFixture::make(teeth: 3),
            'expectedCost' => 1_661,
        ];
        yield [
            'scenario' => 'teeth at level 42: ceil(500 * exp(0.4 * 42)) = 9,888,201,330',
            'upgradable' => Upgradable::Teeth,
            'levels' => UpgradableLevelsFixture::make(teeth: 42),
            'expectedCost' => 9_888_201_330,
        ];

        yield [
            'scenario' => 'legs at level 1: ceil(1,000 * exp(0.6 * 1)) = 1,823',
            'upgradable' => Upgradable::Legs,
            'levels' => UpgradableLevelsFixture::make(legs: 1),
            'expectedCost' => 1_823,
        ];
        yield [
            'scenario' => 'legs at level 2: ceil(1,000 * exp(0.6 * 2)) = 3,321',
            'upgradable' => Upgradable::Legs,
            'levels' => UpgradableLevelsFixture::make(legs: 2),
            'expectedCost' => 3_321,
        ];
        yield [
            'scenario' => 'legs at level 3: ceil(1,000 * exp(0.6 * 3)) = 6,050',
            'upgradable' => Upgradable::Legs,
            'levels' => UpgradableLevelsFixture::make(legs: 3),
            'expectedCost' => 6_050,
        ];
        yield [
            'scenario' => 'legs at level 42: ceil(1,000 * exp(0.6 * 42)) = 87,946,982,651,729',
            'upgradable' => Upgradable::Legs,
            'levels' => UpgradableLevelsFixture::make(legs: 42),
            'expectedCost' => 87_946_982_651_729,
        ];

        yield [
            'scenario' => 'eyes at level 1: ceil(1,000 * exp(0.4 * 1)) = 1,492',
            'upgradable' => Upgradable::Eyes,
            'levels' => UpgradableLevelsFixture::make(eyes: 1),
            'expectedCost' => 1_492,
        ];
        yield [
            'scenario' => 'eyes at level 2: ceil(1,000 * exp(0.4 * 2)) = 2,226',
            'upgradable' => Upgradable::Eyes,
            'levels' => UpgradableLevelsFixture::make(eyes: 2),
            'expectedCost' => 2_226,
        ];
        yield [
            'scenario' => 'eyes at level 3: ceil(1,000 * exp(0.4 * 3)) = 3,321',
            'upgradable' => Upgradable::Eyes,
            'levels' => UpgradableLevelsFixture::make(eyes: 3),
            'expectedCost' => 3_321,
        ];
        yield [
            'scenario' => 'eyes at level 42: ceil(1,000 * exp(0.4 * 42)) = 19,776,402,659',
            'upgradable' => Upgradable::Eyes,
            'levels' => UpgradableLevelsFixture::make(eyes: 42),
            'expectedCost' => 19_776_402_659,
        ];
    }

    #[TestDox('It computes flat cost for bisou $scenario')]
    #[DataProvider('bisouCostProvider')]
    public function test_it_computes_flat_cost_for_bisous(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
        int $expectedCost,
    ): void {
        $this->assertSame($expectedCost, $upgradable->computeCost($levels));
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     *     expectedCost: int,
     * }>
     */
    public static function bisouCostProvider(): \Iterator
    {
        $defaultLevels = UpgradableLevelsFixture::make();

        // Flat cost (buildable quantities, not upgradable levels)
        yield [
            'scenario' => 'peck: 800',
            'upgradable' => Upgradable::Peck,
            'levels' => $defaultLevels,
            'expectedCost' => 800,
        ];
        yield [
            'scenario' => 'smooch: 3,500',
            'upgradable' => Upgradable::Smooch,
            'levels' => $defaultLevels,
            'expectedCost' => 3_500,
        ];
        yield [
            'scenario' => 'french-kiss: 10,000',
            'upgradable' => Upgradable::FrenchKiss,
            'levels' => $defaultLevels,
            'expectedCost' => 10_000,
        ];
    }

    #[TestDox('It computes exponential cost for technique $scenario')]
    #[DataProvider('techniqueCostProvider')]
    public function test_it_computes_exponential_cost_for_techniques(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
        int $expectedCost,
    ): void {
        $this->assertSame($expectedCost, $upgradable->computeCost($levels));
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     *     expectedCost: int,
     * }>
     */
    public static function techniqueCostProvider(): \Iterator
    {
        // Formula: ceil(base * exp(rate * level))

        yield [
            'scenario' => 'hold-breath at level 1: ceil(1,000 * exp(0.4 * 1)) = 1,492',
            'upgradable' => Upgradable::HoldBreath,
            'levels' => UpgradableLevelsFixture::make(holdBreath: 1),
            'expectedCost' => 1_492,
        ];
        yield [
            'scenario' => 'hold-breath at level 2: ceil(1,000 * exp(0.4 * 2)) = 2,226',
            'upgradable' => Upgradable::HoldBreath,
            'levels' => UpgradableLevelsFixture::make(holdBreath: 2),
            'expectedCost' => 2_226,
        ];
        yield [
            'scenario' => 'hold-breath at level 3: ceil(1,000 * exp(0.4 * 3)) = 3,321',
            'upgradable' => Upgradable::HoldBreath,
            'levels' => UpgradableLevelsFixture::make(holdBreath: 3),
            'expectedCost' => 3_321,
        ];
        yield [
            'scenario' => 'hold-breath at level 42: ceil(1,000 * exp(0.4 * 42)) = 19,776,402,659',
            'upgradable' => Upgradable::HoldBreath,
            'levels' => UpgradableLevelsFixture::make(holdBreath: 42),
            'expectedCost' => 19_776_402_659,
        ];

        yield [
            'scenario' => 'flirt at level 1: ceil(2,000 * exp(0.4 * 1)) = 2,984',
            'upgradable' => Upgradable::Flirt,
            'levels' => UpgradableLevelsFixture::make(flirt: 1),
            'expectedCost' => 2_984,
        ];
        yield [
            'scenario' => 'flirt at level 2: ceil(2,000 * exp(0.4 * 2)) = 4,452',
            'upgradable' => Upgradable::Flirt,
            'levels' => UpgradableLevelsFixture::make(flirt: 2),
            'expectedCost' => 4_452,
        ];
        yield [
            'scenario' => 'flirt at level 3: ceil(2,000 * exp(0.4 * 3)) = 6,641',
            'upgradable' => Upgradable::Flirt,
            'levels' => UpgradableLevelsFixture::make(flirt: 3),
            'expectedCost' => 6_641,
        ];
        yield [
            'scenario' => 'flirt at level 42: ceil(2,000 * exp(0.4 * 42)) = 39,552,805,317',
            'upgradable' => Upgradable::Flirt,
            'levels' => UpgradableLevelsFixture::make(flirt: 42),
            'expectedCost' => 39_552_805_317,
        ];

        yield [
            'scenario' => 'spit at level 1: ceil(3,000 * exp(0.4 * 1)) = 4,476',
            'upgradable' => Upgradable::Spit,
            'levels' => UpgradableLevelsFixture::make(spit: 1),
            'expectedCost' => 4_476,
        ];
        yield [
            'scenario' => 'spit at level 2: ceil(3,000 * exp(0.4 * 2)) = 6,677',
            'upgradable' => Upgradable::Spit,
            'levels' => UpgradableLevelsFixture::make(spit: 2),
            'expectedCost' => 6_677,
        ];
        yield [
            'scenario' => 'spit at level 3: ceil(3,000 * exp(0.4 * 3)) = 9,961',
            'upgradable' => Upgradable::Spit,
            'levels' => UpgradableLevelsFixture::make(spit: 3),
            'expectedCost' => 9_961,
        ];
        yield [
            'scenario' => 'spit at level 42: ceil(3,000 * exp(0.4 * 42)) = 59,329,207,976',
            'upgradable' => Upgradable::Spit,
            'levels' => UpgradableLevelsFixture::make(spit: 42),
            'expectedCost' => 59_329_207_976,
        ];

        yield [
            'scenario' => 'leap at level 1: ceil(10,000 * exp(0.6 * 1)) = 18,222',
            'upgradable' => Upgradable::Leap,
            'levels' => UpgradableLevelsFixture::make(leap: 1),
            'expectedCost' => 18_222,
        ];
        yield [
            'scenario' => 'leap at level 2: ceil(10,000 * exp(0.6 * 2)) = 33,202',
            'upgradable' => Upgradable::Leap,
            'levels' => UpgradableLevelsFixture::make(leap: 2),
            'expectedCost' => 33_202,
        ];
        yield [
            'scenario' => 'leap at level 3: ceil(10,000 * exp(0.6 * 3)) = 60,497',
            'upgradable' => Upgradable::Leap,
            'levels' => UpgradableLevelsFixture::make(leap: 3),
            'expectedCost' => 60_497,
        ];
        yield [
            'scenario' => 'leap at level 42: ceil(10,000 * exp(0.6 * 42)) = 879,469,826,517,285',
            'upgradable' => Upgradable::Leap,
            'levels' => UpgradableLevelsFixture::make(leap: 42),
            'expectedCost' => 879_469_826_517_285,
        ];

        yield [
            'scenario' => 'soup at level 1: ceil(5,000 * exp(0.4 * 1)) = 7,460',
            'upgradable' => Upgradable::Soup,
            'levels' => UpgradableLevelsFixture::make(soup: 1),
            'expectedCost' => 7_460,
        ];
        yield [
            'scenario' => 'soup at level 2: ceil(5,000 * exp(0.4 * 2)) = 11,128',
            'upgradable' => Upgradable::Soup,
            'levels' => UpgradableLevelsFixture::make(soup: 2),
            'expectedCost' => 11_128,
        ];
        yield [
            'scenario' => 'soup at level 3: ceil(5,000 * exp(0.4 * 3)) = 16,601',
            'upgradable' => Upgradable::Soup,
            'levels' => UpgradableLevelsFixture::make(soup: 3),
            'expectedCost' => 16_601,
        ];
        yield [
            'scenario' => 'soup at level 42: ceil(5,000 * exp(0.4 * 42)) = 98,882,013,293',
            'upgradable' => Upgradable::Soup,
            'levels' => UpgradableLevelsFixture::make(soup: 42),
            'expectedCost' => 98_882_013_293,
        ];
    }

    #[TestDox('It fails when cost overflows for $scenario')]
    #[DataProvider('overflowProvider')]
    public function test_it_fails_when_cost_overflows(
        string $scenario,
        Upgradable $upgradable,
        UpgradableLevels $levels,
    ): void {
        $this->expectException(ServerErrorException::class);

        $upgradable->computeCost($levels);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     upgradable: Upgradable,
     *     levels: UpgradableLevels,
     * }>
     */
    public static function overflowProvider(): \Iterator
    {
        // exp(0.6 * 1182) = INF, and (int) INF = 0 in PHP
        yield [
            'scenario' => 'leap at level 1182: exp(0.6 * 1182) overflows to INF',
            'upgradable' => Upgradable::Leap,
            'levels' => UpgradableLevelsFixture::make(leap: 1182),
        ];

        // exp(0.4 * 1774) = INF
        yield [
            'scenario' => 'heart at level 1774: exp(0.4 * 1774) overflows to INF',
            'upgradable' => Upgradable::Heart,
            'levels' => UpgradableLevelsFixture::make(heart: 1774),
        ];
    }
}
