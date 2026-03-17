<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Constrained;

use PhpTui\Tui\Layout\Constraint;

/**
 * A widget that declares its own layout constraint.
 *
 * Implement this alongside Widget to let LayoutWidgetRenderer (and similar
 * container renderers) determine each section's size from the widget itself,
 * rather than hard-coding it in the renderer.
 *
 * The constraint must cover the widget's full section height, including any
 * border rows that the container renderer adds around it (typically +2 for
 * Borders::ALL on a BlockWidget).
 */
interface Constrained
{
    public function constraint(): Constraint;
}
