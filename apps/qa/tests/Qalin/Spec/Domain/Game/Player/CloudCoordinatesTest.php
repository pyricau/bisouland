<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Domain\Game\Player;

use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\CloudCoordinatesFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloudCoordinates::class)]
#[Small]
final class CloudCoordinatesTest extends TestCase
{
    #[TestDox('It can be converted from/to ints')]
    public function test_it_can_be_converted_from_and_to_ints(): void
    {
        $x = CloudCoordinatesFixture::makeX();
        $y = CloudCoordinatesFixture::makeY();
        $cloudCoordinates = CloudCoordinates::fromInts($x, $y);

        $this->assertInstanceOf(CloudCoordinates::class, $cloudCoordinates);
        $this->assertSame($x, $cloudCoordinates->getX());
        $this->assertSame($y, $cloudCoordinates->getY());
    }

    #[TestDox('It can be created with default value (1,1)')]
    public function test_it_can_be_created_with_default_value(): void
    {
        $cloudCoordinates = CloudCoordinates::create();

        $this->assertSame(1, $cloudCoordinates->getX());
        $this->assertSame(1, $cloudCoordinates->getY());
    }

    #[TestDox('It fails when raw Cloud Coordinates $scenario')]
    #[DataProvider('invalidCloudCoordinatesProvider')]
    public function test_it_fails_when_raw_cloud_coordinates_is_invalid(
        string $scenario,
        int $x,
        int $y,
    ): void {
        $this->expectException(ValidationFailedException::class);

        CloudCoordinates::fromInts($x, $y);
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      x: int,
     *      y: int,
     *  }>
     */
    public static function invalidCloudCoordinatesProvider(): \Iterator
    {
        yield ['scenario' => 'x is zero (< 1)', 'x' => 0, 'y' => 1];
        yield ['scenario' => 'y is zero (< 1)', 'x' => 1, 'y' => 0];
        yield ['scenario' => 'x is negative (< 0)', 'x' => -1, 'y' => 1];
        yield ['scenario' => 'y is negative (< 0)', 'x' => 1, 'y' => -1];
        yield ['scenario' => 'y is too high (> 16)', 'x' => 1, 'y' => 17];
    }
}
