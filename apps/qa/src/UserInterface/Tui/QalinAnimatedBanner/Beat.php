<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui\QalinAnimatedBanner;

use Bl\Qa\UserInterface\Tui\QalinBanner;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use Symfony\Component\Clock\ClockInterface;

/**
 * The contracted Qalin logo (18 columns wide, 6 rows tall):
 *
 *     ┌──────────────────┐
 *     │                  │
 *     │    ████  ████    │
 *     │   ████████████   │
 *     │     ████████     │
 *     │       ████       │
 *     │                  │
 *     └──────────────────┘
 */
final class Beat implements Animation
{
    private const float BEAT_ON_SECONDS = 0.15;

    private const float BEAT_OFF_SECONDS = 0.1;

    private const int BEAT_COUNT = 2;

    /** @var list<string> */
    public const array CONTRACTED_LOGO = [
        '                  ',
        '    ████  ████    ',
        '   ████████████   ',
        '     ████████     ',
        '       ████       ',
        '                  ',
    ];

    private ?float $beatStartedAt = null;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function animate(): void
    {
        $this->beatStartedAt = $this->now();
    }

    public function logo(): array
    {
        return $this->isBeating()
            ? self::CONTRACTED_LOGO
            : QalinBanner::LOGO;
    }

    public function logoStyle(): Style
    {
        return $this->isBeating()
            ? Style::default()->fg(AnsiColor::Magenta)
            : Style::default()->fg(AnsiColor::Red);
    }

    private function isBeating(): bool
    {
        if (null === $this->beatStartedAt) {
            return false;
        }

        $elapsed = $this->now() - $this->beatStartedAt;
        $cycleSeconds = self::BEAT_ON_SECONDS + self::BEAT_OFF_SECONDS;
        $totalSeconds = self::BEAT_COUNT * $cycleSeconds;

        if ($elapsed >= $totalSeconds) {
            $this->beatStartedAt = null;

            return false;
        }

        $position = fmod($elapsed, $cycleSeconds);

        return $position < self::BEAT_ON_SECONDS;
    }

    private function now(): float
    {
        return (float) $this->clock->now()->format('U.u');
    }
}
