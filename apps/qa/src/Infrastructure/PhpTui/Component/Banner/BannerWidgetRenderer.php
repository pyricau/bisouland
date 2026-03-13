<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Banner;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders BannerWidget as a horizontal split: logo | slogan.
 *
 * The logo width is derived from the length of its strings.
 * The slogan is indented by 3 spaces to create a visual gap from the logo.
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new BannerWidgetRenderer())
 *         ->build();
 */
final class BannerWidgetRenderer implements WidgetRenderer
{
    private const SLOGAN_GAP = 3;

    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof BannerWidget) {
            return;
        }

        $logoWidth = mb_strlen($widget->logo[0] ?? '');
        $logoLines = array_map(
            static fn (string $line): Line => Line::fromSpans(Span::styled($line, $widget->logoStyle)),
            $widget->logo,
        );
        $slogan = array_map(
            static fn (string $line): Line => Line::fromSpans(
                Span::fromString(str_repeat(' ', self::SLOGAN_GAP)),
                Span::styled($line, $widget->sloganStyle),
            ),
            $widget->slogan,
        );

        $bannerWidget = match (true) {
            0 === $logoWidth => ParagraphWidget::fromLines(...$slogan),
            [] === $slogan || $area->width <= $logoWidth => ParagraphWidget::fromLines(...$logoLines),
            default => GridWidget::default()
                ->direction(Direction::Horizontal)
                ->constraints(
                    Constraint::length($logoWidth),
                    Constraint::min(0),
                )
                ->widgets(
                    ParagraphWidget::fromLines(...$logoLines),
                    ParagraphWidget::fromLines(...$slogan),
                ),
        };

        $renderer->render($renderer, $bannerWidget, $buffer, $area);
    }
}
