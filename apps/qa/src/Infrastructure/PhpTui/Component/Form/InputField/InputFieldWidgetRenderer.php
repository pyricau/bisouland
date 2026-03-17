<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders InputFieldWidget as a labeled block wrapping a LineEditorWidget:
 *     ╭Username────────╮
 *     │blackadder█     │
 *     ╰────────────────╯
 *
 * Delegates the text + cursor rendering to LineEditorWidgetRenderer.
 *
 * Registration (both renderers are required):
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new InputFieldWidgetRenderer())
 *         ->addWidgetRenderer(new LineEditorWidgetRenderer())
 *         ->build();
 */
final class InputFieldWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof InputFieldWidget) {
            return;
        }

        $block = BlockWidget::default()
            ->borders(Borders::ALL)
            ->borderType(BorderType::Rounded)
            ->borderStyle(
                $widget->focused
                    ? $widget->focusedBorderStyle
                    : $widget->unfocusedBorderStyle,
            )
            ->titles(Title::fromString($widget->label));

        $renderer->render(
            $renderer,
            $block->widget($widget->lineEditorWidget),
            $buffer,
            $area,
        );
    }
}
