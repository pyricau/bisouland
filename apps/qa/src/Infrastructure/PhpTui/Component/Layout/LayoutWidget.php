<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Layout;

use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\Constrained;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that arranges the full-screen layout: banner, navbar, body, footer.
 *
 * Banner, navbar and footer must implement Constrained so that LayoutWidgetRenderer
 * can determine their section heights without knowing their concrete types.
 * The body fills all remaining space (Constraint::min(0)) by design.
 *
 * Usage:
 *     $widget = LayoutWidget::from(
 *         BannerWidget::from(['██▀▀▄', '██▄▄▀', '██▀▀▄'], 'I have a cunning plan'),
 *         HotkeyTabsWidget::fromTabs(['1' => 'TabA', '2' => 'TabB']),
 *         ParagraphWidget::fromString('body content here'),
 *         KeyHintsWidget::from(['Next' => 'Tab', 'Select' => 'Enter', 'Quit' => 'Esc']),
 *     );
 */
final readonly class LayoutWidget implements Widget
{
    private function __construct(
        public Widget&Constrained $banner,
        public Widget&Constrained $navbar,
        public Widget $body,
        public Widget&Constrained $footer,
    ) {
    }

    public static function from(
        Widget&Constrained $banner,
        Widget&Constrained $navbar,
        Widget $body,
        Widget&Constrained $footer,
    ): self {
        return new self($banner, $navbar, $body, $footer);
    }
}
