<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;

/**
 * The Qalin logo (18 columns wide, 6 rows tall):
 *
 *     ┌──────────────────┐
 *     │  ████      ████  │
 *     │████████  ████████│
 *     │██████████████████│
 *     │██████████████████│
 *     │   ████████████   │
 *     │      ██████      │
 *     └──────────────────┘
 */
final readonly class QalinBanner
{
    /** @var list<string> */
    public const SLOGAN = [
        "Qalin (it's pronounced câlin)",
        'Quality Assurance Local Interface Nudger',
        "(BisouLand's Test Control Interface)",
    ];

    /** @var list<string> */
    public const LOGO = [
        '  ████      ████  ',
        '████████  ████████',
        '██████████████████',
        '██████████████████',
        '   ████████████   ',
        '      ██████      ',
    ];

    public static function widget(): BannerWidget
    {
        return BannerWidget::from(self::LOGO, ...self::SLOGAN)
            ->logoStyle(Style::default()->fg(AnsiColor::Red));
    }
}
