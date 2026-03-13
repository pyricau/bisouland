<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Banner;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\Constrained;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Layout\Constraint\LengthConstraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a logo and slogan banner.
 *
 * The logo is an array of strings, all of the same length.
 *
 * Usage:
 *     $widget = BannerWidget::from(
 *         [
 *             '██▀▀▄',
 *             '██▄▄▀',
 *             '██▀▀▄',
 *         ],
 *         [
 *             "We're in the stickiest situation",
 *             "since Sticky the Stick Insect",
 *             "got stuck on a sticky bun",
 *         ]
 *     );
 *
 *     // Styles can be customized:
 *     $widget = $widget->logoStyle($style)->sloganStyle($style);
 */
final readonly class BannerWidget implements Widget, Constrained
{
    /**
     * @param list<string> $logo   strings of equal length
     * @param list<string> $slogan
     */
    private function __construct(
        public array $logo,
        public array $slogan,
        public Style $logoStyle,
        public Style $sloganStyle,
    ) {
    }

    /**
     * @param list<string> $logo strings of equal length
     */
    public static function from(array $logo, string ...$slogan): self
    {
        if ([] !== $logo) {
            $expectedLength = mb_strlen($logo[0]);
            foreach ($logo as $i => $line) {
                $length = mb_strlen($line);
                if ($length !== $expectedLength) {
                    throw ValidationFailedException::make(
                        "Invalid \"BannerWidget\" parameter: all logo strings must have the same length ({$expectedLength} expected, got {$length} for logo[{$i}])",
                    );
                }
            }
        }

        return new self(
            $logo,
            array_values($slogan),
            Style::default()->fg(AnsiColor::Yellow),
            Style::default(),
        );
    }

    public function constraint(): LengthConstraint
    {
        return Constraint::length(\count($this->logo) + 2);
    }

    public function logoStyle(Style $logoStyle): self
    {
        return new self($this->logo, $this->slogan, $logoStyle, $this->sloganStyle);
    }

    public function sloganStyle(Style $sloganStyle): self
    {
        return new self($this->logo, $this->slogan, $this->logoStyle, $sloganStyle);
    }
}
