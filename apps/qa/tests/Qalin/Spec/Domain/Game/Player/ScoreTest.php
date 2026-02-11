<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\Score;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\ScoreFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Score::class)]
#[Small]
final class ScoreTest extends TestCase
{
    #[TestDox('It can be converted from/to int')]
    public function test_it_can_be_converted_from_and_to_int(): void
    {
        $intScore = ScoreFixture::makeInt();
        $score = Score::fromInt($intScore);

        $this->assertInstanceOf(Score::class, $score);
        $this->assertSame($intScore, $score->toInt());
    }

    #[TestDox('It can be created with default value (0)')]
    public function test_it_can_be_created_with_default_value(): void
    {
        $score = Score::create();

        $this->assertSame(0, $score->toInt());
    }

    #[TestDox('It fails when raw Score is negative (< 0)')]
    public function test_it_fails_when_raw_score_is_negative(): void
    {
        $this->expectException(ValidationFailedException::class);

        Score::fromInt(-1);
    }
}
