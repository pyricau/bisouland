<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken;

use Bl\Domain\Auth\AuthToken\ExpiresAt;
use Bl\Domain\Exception\ValidationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExpiresAt::class)]
#[Small]
final class ExpiresAtTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        /** @var array<string, string> $iso8601s */
        $iso8601s = [
            '2025-06-17T13:00:00' => 'Y-m-d\\TH:i:s',
            '2025-06-17T13:00:00Z' => 'Y-m-d\\TH:i:s\\Z',
            '2025-06-17T13:00:00+00:00' => 'Y-m-d\\TH:i:sP',
            '2025-06-17T13:00:00-05:00' => 'Y-m-d\\TH:i:sP',
            '2025-06-17T13:00:00.123456' => 'Y-m-d\\TH:i:s.u',
            '2025-06-17T13:00:00.123456Z' => 'Y-m-d\\TH:i:s.u\\Z',
            '2025-06-17T13:00:00.123456+02:00' => 'Y-m-d\\TH:i:s.uP',
        ];

        foreach ($iso8601s as $stringExpiresAt => $format) {
            $expiresAt = ExpiresAt::fromString($stringExpiresAt);

            $this->assertInstanceOf(ExpiresAt::class, $expiresAt);
            $this->assertSame($stringExpiresAt, $expiresAt->toString($format));
        }
    }

    public function test_it_creates_with_default_duration(): void
    {
        $expectedTimestamp = new \DateTimeImmutable(ExpiresAt::DEFAULT_DURATION)->getTimestamp();

        $expiresAt = ExpiresAt::create();

        $this->assertInstanceOf(ExpiresAt::class, $expiresAt);
        // Allow 5 seconds tolerance for test execution time
        $this->assertEqualsWithDelta(
            $expectedTimestamp,
            $expiresAt->toTimestamp(),
            5,
            'ExpiresAt should be created with the default duration',
        );
    }

    #[TestDox('It can be converted from/to integer UNIX timestamp')]
    public function test_it_can_be_converted_from_and_to_integer_unix_timestamp(): void
    {
        $integerUnixTimestamp = new \DateTimeImmutable(ExpiresAt::DEFAULT_DURATION)->getTimestamp();

        $expiresAt = ExpiresAt::fromTimestamp($integerUnixTimestamp);

        $this->assertInstanceOf(ExpiresAt::class, $expiresAt);
        $this->assertSame($integerUnixTimestamp, $expiresAt->toTimestamp());
    }

    #[TestDox('It can be created from DateTime')]
    public function test_it_can_be_created_from_date_time(): void
    {
        $dateTime = new \DateTimeImmutable('2025-06-17T13:00:00.123456+02:00');
        $expiresAt = ExpiresAt::fromDateTime($dateTime);

        $this->assertInstanceOf(ExpiresAt::class, $expiresAt);
        $this->assertSame(
            $dateTime->format(ExpiresAt::ISO_8601),
            $expiresAt->toString(ExpiresAt::ISO_8601),
        );
    }

    #[TestDox('It fails when it $scenario')]
    #[DataProvider('invalidIso8601Provider')]
    public function test_it_fails_when_it_is_not_a_valid_iso_8601_date(
        string $scenario,
        string $invalidDate,
    ): void {
        $this->expectException(ValidationFailedException::class);

        ExpiresAt::fromString($invalidDate);
    }

    /**
     * @return \Iterator<int|string, array{string, string}>
     */
    public static function invalidIso8601Provider(): \Iterator
    {
        yield ['is invalid date', 'invalid-date'];
        yield ['has invalid month', '2025-13-17T13:00:00'];
        yield ['has invalid day', '2025-06-32T13:00:00'];
        yield ['has invalid hour', '2025-06-17T25:00:00'];
        yield ['has invalid minute', '2025-06-17T13:61:00'];
        yield ['has invalid second', '2025-06-17T13:00:61'];
        yield ['has wrong format', '2025/06/17 13:00:00'];
        yield ['has wrong date order', '17-06-2025T13:00:00'];
        yield ['is missing T', '2025-06-17 13:00:00'];
        yield ['is missing seconds', '2025-06-17T13:00'];
        yield ['has single digit month', '2025-6-17T13:00:00'];
        yield ['has single digit day', '2025-06-7T13:00:00'];
    }
}
