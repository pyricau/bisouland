<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders SubmitFieldWidget as styled text:
 *     [ Submit ]
 *
 * The style switches between focusedStyle and unfocusedStyle.
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new SubmitFieldWidgetRenderer())
 *         ->build();
 */
final class SubmitFieldWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof SubmitFieldWidget) {
            return;
        }

        $style = $widget->focused ? $widget->focusedStyle : $widget->unfocusedStyle;

        $renderer->render(
            $renderer,
            ParagraphWidget::fromSpans(Span::styled("[ {$widget->label} ]", $style)),
            $buffer,
            $area,
        );
    }
}
