<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Constrained;

use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\ConstrainedWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConstrainedWidget::class)]
#[Small]
final class ConstrainedWidgetTest extends TestCase
{
    #[TestDox('It has widget (e.g. ParagraphWidget)')]
    public function test_it_has_widget(): void
    {
        $inner = ParagraphWidget::fromString('Title');
        $widget = ConstrainedWidget::wrap($inner, Constraint::length(3));

        $this->assertSame($inner, $widget->widget);
    }

    #[TestDox('It has constraint (e.g. Constraint::length(3))')]
    public function test_it_has_constraint(): void
    {
        $constraint = Constraint::length(3);
        $widget = ConstrainedWidget::wrap(ParagraphWidget::fromString('Title'), $constraint);

        $this->assertEquals($constraint, $widget->constraint());
    }
}
