<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders ChoiceFieldWidget as a labeled block wrapping a filter input and a choice list:
 *     ╭Language──────────╮
 *     │ph█               │
 *     │> PHP             │
 *     │  Python          │
 *     │  JavaScript      │
 *     ╰──────────────────╯
 *
 * Delegates the filter text + cursor rendering to LineEditorWidgetRenderer.
 *
 * Registration (all renderers are required):
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new ChoiceFieldWidgetRenderer())
 *         ->addWidgetRenderer(new LineEditorWidgetRenderer())
 *         ->build();
 */
final class ChoiceFieldWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof ChoiceFieldWidget) {
            return;
        }

        $listItems = array_map(
            static fn (string $choice): ListItem => ListItem::fromString($choice),
            $widget->choices,
        );

        $listWidget = ListWidget::default()
            ->items(...$listItems)
            ->highlightStyle($widget->highlightStyle)
            ->highlightSymbol('> ')
            ->select($widget->selectedIndex);

        $innerWidget = GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(1),
                Constraint::min(0),
            )
            ->widgets($widget->lineEditorWidget, $listWidget);

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
            $block->widget($innerWidget),
            $buffer,
            $area,
        );
    }
}
