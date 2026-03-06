<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Constrained;

use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Widget;

/**
 * Wraps any widget with an explicit layout constraint, making it usable in
 * constrained layout slots (e.g. LayoutWidget::from() banner/navbar/footer).
 *
 * Use this when you have a plain widget (e.g. ParagraphWidget) that cannot
 * declare its own constraint, but you know the height it should occupy.
 *
 * Usage:
 *     $nav = ConstrainedWidget::wrap(
 *         ParagraphWidget::fromString('My title'),
 *         Constraint::length(3),
 *     );
 *
 * Registration (in WidgetRenderer chain):
 *     new ConstrainedWidgetRenderer()
 */
final readonly class ConstrainedWidget implements Widget, Constrained
{
    private function __construct(
        public Widget $widget,
        private Constraint $constraint,
    ) {
    }

    public static function wrap(Widget $widget, Constraint $constraint): self
    {
        return new self($widget, $constraint);
    }

    public function constraint(): Constraint
    {
        return $this->constraint;
    }
}
