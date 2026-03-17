<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\KeyValue;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\Paragraph\Wrap;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders KeyValueWidget as a list of "key: value" lines.
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new KeyValueWidgetRenderer())
 *         ->build();
 */
final class KeyValueWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof KeyValueWidget) {
            return;
        }

        $lines = [];
        foreach ($widget->rows as $key => $value) {
            $lines[] = Line::fromSpans(
                Span::styled("{$key}: ", $widget->keyStyle),
                Span::fromString((string) $value),
            );
        }

        $paragraph = [] !== $lines ? ParagraphWidget::fromLines(...$lines) : ParagraphWidget::fromString('');

        $renderer->render(
            $renderer,
            $paragraph->wrap(Wrap::Word),
            $buffer,
            $area,
        );
    }
}
