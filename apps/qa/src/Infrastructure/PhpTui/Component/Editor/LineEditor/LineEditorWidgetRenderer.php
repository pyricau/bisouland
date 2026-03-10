<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders LineEditorWidget as text with an optional cursor.
 *
 * When focused, the character at cursorPosition is highlighted with REVERSED style:
 *     baldrick█
 *
 * When not focused, renders the value as plain text:
 *     baldrick
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new LineEditorWidgetRenderer())
 *         ->build();
 */
final class LineEditorWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof LineEditorWidget) {
            return;
        }

        if (!$widget->focused) {
            $renderer->render($renderer, ParagraphWidget::fromString($widget->value), $buffer, $area);

            return;
        }

        // Split value around cursor, render the cursor char with REVERSED style.
        // e.g. "baldrick" with cursor at end: "baldrick" + REVERSED(" ")
        // e.g. "baldrick" with cursor at 0: REVERSED("b") + "aldrick"
        $before = mb_substr($widget->value, 0, $widget->cursorPosition);
        $cursorChar = mb_substr($widget->value, $widget->cursorPosition, 1) ?: ' ';
        $after = mb_substr($widget->value, $widget->cursorPosition + 1);

        $renderer->render(
            $renderer,
            ParagraphWidget::fromLines(
                Line::fromSpans(
                    Span::fromString($before),
                    Span::styled($cursorChar, Style::default()->addModifier(Modifier::REVERSED)),
                    Span::fromString($after),
                ),
            ),
            $buffer,
            $area,
        );
    }
}
