<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\UserInterface\Tui\QalinAnimatedBanner;

use Bl\Qa\UserInterface\Tui\QalinAnimatedBanner\Beat;
use Bl\Qa\UserInterface\Tui\QalinBanner;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(Beat::class)]
final class BeatTest extends TestCase
{
    #[DataProvider('frame0Provider')]
    #[TestDox('It renders frame 0: $scenario')]
    public function test_it_renders_logo_frame_0(string $scenario, int $index, string $line): void
    {
        $mockClock = new MockClock('2024-01-01 00:00:00');
        $beat = new Beat($mockClock);

        $this->assertSame($line, $beat->logo()[$index]);
        $this->assertEquals(Style::default()->fg(AnsiColor::Red), $beat->logoStyle());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     index: int,
     *     line: string,
     * }>
     */
    public static function frame0Provider(): \Generator
    {
        $lines = QalinBanner::LOGO;

        yield [
            'scenario' => 'before animate(): default logo, in red',
            'index' => 0,
            'line' => $lines[0],
        ];

        foreach ($lines as $index => $line) {
            yield [
                'scenario' => "`{$line}`",
                'index' => $index,
                'line' => $line,
            ];
        }
    }

    #[DataProvider('frame1Provider')]
    #[TestDox('It renders frame 1: $scenario')]
    public function test_it_renders_logo_frame_1(
        string $scenario,
        int $index,
        string $line,
    ): void {
        $mockClock = new MockClock('2024-01-01 00:00:00');
        $beat = new Beat($mockClock);
        $beat->animate();

        $this->assertSame($line, $beat->logo()[$index]);
        $this->assertEquals(Style::default()->fg(AnsiColor::Magenta), $beat->logoStyle());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     index: int,
     *     line: string,
     * }>
     */
    public static function frame1Provider(): \Generator
    {
        $lines = Beat::CONTRACTED_LOGO;

        yield [
            'scenario' => 'at t=0s: contracted logo, in magenta',
            'index' => 0,
            'line' => $lines[0],
        ];

        foreach ($lines as $index => $line) {
            yield [
                'scenario' => "`{$line}`",
                'index' => $index,
                'line' => $line,
            ];
        }
    }

    #[DataProvider('frame2Provider')]
    #[TestDox('It renders frame 2: $scenario')]
    public function test_it_renders_logo_frame_2(
        string $scenario,
        int $index,
        string $line,
    ): void {
        $mockClock = new MockClock('2024-01-01 00:00:00');
        $beat = new Beat($mockClock);
        $beat->animate();

        $mockClock->sleep(0.151);

        $this->assertSame($line, $beat->logo()[$index]);
        $this->assertEquals(Style::default()->fg(AnsiColor::Red), $beat->logoStyle());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     index: int,
     *     line: string,
     * }>
     */
    public static function frame2Provider(): \Generator
    {
        $lines = QalinBanner::LOGO;

        yield [
            'scenario' => 'at t=0.151s: default logo, in red',
            'index' => 0,
            'line' => $lines[0],
        ];

        foreach ($lines as $index => $line) {
            yield [
                'scenario' => "`{$line}`",
                'index' => $index,
                'line' => $line,
            ];
        }
    }

    #[DataProvider('frame3Provider')]
    #[TestDox('It renders frame 3: $scenario')]
    public function test_it_renders_logo_frame_3(
        string $scenario,
        int $index,
        string $line,
    ): void {
        $mockClock = new MockClock('2024-01-01 00:00:00');
        $beat = new Beat($mockClock);
        $beat->animate();

        $mockClock->sleep(0.25);

        $this->assertSame($line, $beat->logo()[$index]);
        $this->assertEquals(Style::default()->fg(AnsiColor::Magenta), $beat->logoStyle());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     index: int,
     *     line: string,
     * }>
     */
    public static function frame3Provider(): \Generator
    {
        $lines = Beat::CONTRACTED_LOGO;

        yield [
            'scenario' => 'at t=0.25s: contracted logo, in magenta',
            'index' => 0,
            'line' => $lines[0],
        ];

        foreach ($lines as $index => $line) {
            yield [
                'scenario' => "`{$line}`",
                'index' => $index,
                'line' => $line,
            ];
        }
    }

    #[DataProvider('frame4Provider')]
    #[TestDox('It renders frame 4: $scenario')]
    public function test_it_renders_logo_frame_4(
        string $scenario,
        int $index,
        string $line,
    ): void {
        $mockClock = new MockClock('2024-01-01 00:00:00');
        $beat = new Beat($mockClock);
        $beat->animate();

        $mockClock->sleep(1.0);

        $this->assertSame($line, $beat->logo()[$index]);
        $this->assertEquals(Style::default()->fg(AnsiColor::Red), $beat->logoStyle());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     index: int,
     *     line: string,
     * }>
     */
    public static function frame4Provider(): \Generator
    {
        $lines = QalinBanner::LOGO;

        yield [
            'scenario' => 'at t=1.0s: default logo, in red',
            'index' => 0,
            'line' => $lines[0],
        ];

        foreach ($lines as $index => $line) {
            yield [
                'scenario' => "`{$line}`",
                'index' => $index,
                'line' => $line,
            ];
        }
    }
}
