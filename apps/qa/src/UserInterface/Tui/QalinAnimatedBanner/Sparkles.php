<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui\QalinAnimatedBanner;

use Bl\Qa\UserInterface\Tui\QalinBanner;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use Symfony\Component\Clock\ClockInterface;

final class Sparkles implements Animation
{
    private const SPARKLE_ON_SECONDS = 0.15;

    private const SPARKLE_OFF_SECONDS = 0.1;

    private const SPARKLE_COUNT = 2;

    private ?float $sparklesStartedAt = null;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function animate(): void
    {
        $this->sparklesStartedAt = $this->now();
    }

    public function logo(): array
    {
        if (false === $this->isSparkling()) {
            return QalinBanner::LOGO;
        }

        return match ($this->sparklePhase()) {
            0 => $this->withSparkles([0, 17, '✦']),
            1 => $this->withSparkles([5, 17, '✧'], [0, 0, '✶']),
            default => QalinBanner::LOGO,
        };
    }

    public function logoStyle(): Style
    {
        return Style::default()->fg(AnsiColor::Red);
    }

    private function isSparkling(): bool
    {
        if (null === $this->sparklesStartedAt) {
            return false;
        }

        $elapsed = $this->now() - $this->sparklesStartedAt;
        $cycleSeconds = self::SPARKLE_ON_SECONDS + self::SPARKLE_OFF_SECONDS;
        $totalSeconds = self::SPARKLE_COUNT * $cycleSeconds;

        if ($elapsed >= $totalSeconds) {
            $this->sparklesStartedAt = null;

            return false;
        }

        $position = fmod($elapsed, $cycleSeconds);

        return $position < self::SPARKLE_ON_SECONDS;
    }

    private function sparklePhase(): int
    {
        $elapsed = $this->now() - $this->sparklesStartedAt;
        $cycleSeconds = self::SPARKLE_ON_SECONDS + self::SPARKLE_OFF_SECONDS;

        return (int) floor($elapsed / $cycleSeconds);
    }

    /**
     * @param array{0:int,1:int,2:string} ...$positions
     *
     * @return list<string>
     */
    private function withSparkles(array ...$positions): array
    {
        $logo = QalinBanner::LOGO;

        foreach ($positions as [$row, $column, $sparkle]) {
            $logo[$row] = $this->replaceAt($logo[$row], $column, $sparkle);
        }

        return array_values($logo);
    }

    private function replaceAt(string $line, int $column, string $replacement): string
    {
        return mb_substr($line, 0, $column)
            .$replacement
            .mb_substr($line, $column + 1);
    }

    private function now(): float
    {
        return (float) $this->clock->now()->format('U.u');
    }
}
