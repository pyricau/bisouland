<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\LovePointsFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LovePoints::class)]
#[Small]
final class LovePointsTest extends TestCase
{
    #[TestDox('It can be converted from/to int')]
    public function test_it_can_be_converted_from_and_to_int(): void
    {
        $intLovePoints = LovePointsFixture::makeInt();
        $lovePoints = LovePoints::fromInt($intLovePoints);

        $this->assertInstanceOf(LovePoints::class, $lovePoints);
        $this->assertSame($intLovePoints, $lovePoints->toInt());
    }

    #[TestDox('It can be created at its starting value (300)')]
    public function test_it_can_be_created_at_its_starting_value(): void
    {
        $lovePoints = LovePoints::create();

        $this->assertSame(300, $lovePoints->toInt());
    }

    #[TestDox('It fails when raw Love Points is negative (< 0)')]
    public function test_it_fails_when_raw_love_points_is_negative(): void
    {
        $this->expectException(ValidationFailedException::class);

        LovePoints::fromInt(-1);
    }
}
