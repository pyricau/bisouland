<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form;

use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders FormWidget as a vertical grid of items.
 *
 * Heights per item type:
 *   - ChoiceFieldWidget: 3 + count(choices) rows (1 filter + N choices + 2 border lines)
 *   - InputFieldWidget:  3 rows (1 content + 2 border lines)
 *   - anything else:     1 row
 *
 * Registration (all child renderers are required):
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new FormWidgetRenderer())
 *         ->addWidgetRenderer(new ChoiceFieldWidgetRenderer())
 *         ->addWidgetRenderer(new InputFieldWidgetRenderer())
 *         ->addWidgetRenderer(new LineEditorWidgetRenderer())
 *         ->addWidgetRenderer(new SubmitFieldWidgetRenderer())
 *         ->build();
 */
final class FormWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof FormWidget) {
            return;
        }

        $constraints = array_map(
            static fn (Widget $item): Constraint => match (true) {
                $item instanceof ChoiceFieldWidget => Constraint::length(3 + \count($item->choices)),
                $item instanceof InputFieldWidget => Constraint::length(3),
                default => Constraint::length(1),
            },
            $widget->items,
        );

        $renderer->render(
            $renderer,
            GridWidget::default()
                ->direction(Direction::Vertical)
                ->constraints(...$constraints)
                ->widgets(...$widget->items),
            $buffer,
            $area,
        );
    }
}
