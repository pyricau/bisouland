<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Constrained;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders a ConstrainedWidget by delegating to its inner widget.
 *
 * @see ConstrainedWidget
 */
final class ConstrainedWidgetRenderer implements WidgetRenderer
{
    public function render(WidgetRenderer $renderer, Widget $widget, Buffer $buffer, Area $area): void
    {
        if (!$widget instanceof ConstrainedWidget) {
            return;
        }

        $renderer->render($renderer, $widget->widget, $buffer, $area);
    }
}
