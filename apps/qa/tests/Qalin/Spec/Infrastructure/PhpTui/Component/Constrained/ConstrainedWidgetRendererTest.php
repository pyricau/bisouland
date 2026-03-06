<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Constrained;

use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\ConstrainedWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\ConstrainedWidgetRenderer;
use PhpTui\Tui\Canvas\AggregateShapePainter;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\CoreExtension;
use PhpTui\Tui\Extension\Core\Widget\CanvasRenderer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\WidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\AggregateWidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\NullWidgetRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConstrainedWidgetRenderer::class)]
#[Small]
final class ConstrainedWidgetRendererTest extends TestCase
{
    #[TestDox('It renders: `Body      ` (delegates to inner ParagraphWidget)')]
    public function test_it_renders_delegating_to_inner_widget(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));

        $this->renderer()->render(
            new NullWidgetRenderer(),
            ConstrainedWidget::wrap(ParagraphWidget::fromString('Body'), Constraint::length(1)),
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(['Body      '], $buffer->toLines());
    }

    #[TestDox('It ignores unsupported widgets')]
    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));

        new ConstrainedWidgetRenderer()->render(
            new NullWidgetRenderer(),
            ParagraphWidget::fromString('Ignored'),
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(['          '], $buffer->toLines());
    }

    private function renderer(): WidgetRenderer
    {
        $coreExtension = new CoreExtension();

        return new AggregateWidgetRenderer([
            new CanvasRenderer(
                new AggregateShapePainter($coreExtension->shapePainters()),
            ),
            new ConstrainedWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
