<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\KeyHints;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders KeyHintsWidget as a single line of "Action:Key | Action:Key" spans.
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new KeyHintsWidgetRenderer())
 *         ->build();
 */
final class KeyHintsWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof KeyHintsWidget) {
            return;
        }

        $spans = [];
        foreach ($widget->keyHints as $action => $key) {
            if ([] !== $spans) {
                $spans[] = Span::styled(' | ', $widget->actionStyle);
            }

            $spans[] = Span::styled("{$action}:", $widget->actionStyle);
            $spans[] = Span::styled($key, $widget->keyStyle);
        }

        $renderer->render(
            $renderer,
            ParagraphWidget::fromLines(Line::fromSpans(...$spans)),
            $buffer,
            $area,
        );
    }
}
