<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\MilliScore;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\MilliScoreFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(MilliScore::class)]
#[Small]
final class MilliScoreTest extends TestCase
{
    #[TestDox('It can be converted from/to int')]
    public function test_it_can_be_converted_from_and_to_int(): void
    {
        $intMilliScore = MilliScoreFixture::makeInt();
        $milliScore = MilliScore::fromInt($intMilliScore);

        $this->assertInstanceOf(MilliScore::class, $milliScore);
        $this->assertSame($intMilliScore, $milliScore->toInt());
    }

    #[TestDox('It can be created with default value (0)')]
    public function test_it_can_be_created_with_default_value(): void
    {
        $milliScore = MilliScore::create();

        $this->assertSame(0, $milliScore->toInt());
    }

    #[DataProvider('toScoreProvider')]
    #[TestDox('It converts to Score: $scenario')]
    public function test_it_converts_to_score(string $scenario, int $milliScore, int $expectedScore): void
    {
        $this->assertSame($expectedScore, MilliScore::fromInt($milliScore)->toScore());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     milliScore: int,
     *     expectedScore: int,
     * }>
     */
    public static function toScoreProvider(): \Iterator
    {
        // Formula: floor(milli_score / 1000)

        yield [
            'scenario' => 'floor(0 / 1000) = 0',
            'milliScore' => 0,
            'expectedScore' => 0,
        ];
        yield [
            'scenario' => 'floor(999 / 1000) = 0',
            'milliScore' => 999,
            'expectedScore' => 0,
        ];
        yield [
            'scenario' => 'floor(1000 / 1000) = 1',
            'milliScore' => 1000,
            'expectedScore' => 1,
        ];
        yield [
            'scenario' => 'floor(1500 / 1000) = 1',
            'milliScore' => 1500,
            'expectedScore' => 1,
        ];
        yield [
            'scenario' => 'floor(5,998,666,735 / 1000) = 5,998,666',
            'milliScore' => 5_998_666_735,
            'expectedScore' => 5_998_666,
        ];
    }

    #[TestDox('It fails when raw MilliScore is negative (< 0)')]
    public function test_it_fails_when_raw_milli_score_is_negative(): void
    {
        $this->expectException(ValidationFailedException::class);

        MilliScore::fromInt(-1);
    }
}
