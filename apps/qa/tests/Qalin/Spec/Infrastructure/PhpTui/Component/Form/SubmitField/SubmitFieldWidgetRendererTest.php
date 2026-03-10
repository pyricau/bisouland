<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\SubmitField;

use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidgetRenderer;
use PhpTui\Tui\Canvas\AggregateShapePainter;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\CoreExtension;
use PhpTui\Tui\Extension\Core\Widget\CanvasRenderer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Position\Position;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\AggregateWidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\NullWidgetRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubmitFieldWidgetRenderer::class)]
#[Small]
final class SubmitFieldWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('buttonsProvider')]
    #[TestDox('It renders: $scenario')]
    public function test_it_renders(
        string $scenario,
        Area $area,
        Widget $widget,
        array $expected,
    ): void {
        $buffer = Buffer::empty($area);

        $this->render($buffer, $widget);

        $this->assertSame($expected, $buffer->toLines());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     area: Area,
     *     widget: Widget,
     *     expected: list<string>,
     * }>
     */
    public static function buttonsProvider(): \Generator
    {
        yield [
            'scenario' => '`[ Submit ]` (label wrapped in brackets)',
            'area' => Area::fromDimensions(10, 1),
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
            'expected' => [
                '[ Submit ]',
            ],
        ];
        yield [
            'scenario' => '`[ Submit ]    ` (4 spaces padding on right in 14-wide area)',
            'area' => Area::fromDimensions(14, 1),
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
            'expected' => [
                '[ Submit ]    ',
            ],
        ];
        yield [
            'scenario' => '`[ Submit ` (9-wide: right-clipped, closing `]` lost)',
            'area' => Area::fromDimensions(9, 1),
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
            'expected' => [
                '[ Submit ',
            ],
        ];
        yield [
            'scenario' => '`[ Sub` (5-wide: truncated mid-content)',
            'area' => Area::fromDimensions(5, 1),
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
            'expected' => [
                '[ Sub',
            ],
        ];
    }

    #[DataProvider('stylesProvider')]
    #[TestDox('It renders style: $scenario')]
    public function test_it_renders_style(
        string $scenario,
        Widget $widget,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));
        $this->render($buffer, $widget);

        $cell = $buffer->get(Position::at(0, 0)); // '[ Submit ]' is 10 chars, fills the 10-wide area exactly
        $this->assertSame($expectedFg, $cell->fg);
        $this->assertSame($expectedModifiers, $cell->modifiers);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: Widget,
     *     expectedFg: AnsiColor,
     *     expectedModifiers: int,
     * }>
     */
    public static function stylesProvider(): \Generator
    {
        yield [
            'scenario' => 'focusedStyle in yellow',
            'widget' => SubmitFieldWidget::fromLabel('Submit')->focused(),
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'unfocusedStyle in plain (terminal reset)',
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));
        $widget = ParagraphWidget::fromString('Ignored');

        new SubmitFieldWidgetRenderer()->render(
            new NullWidgetRenderer(),
            $widget,
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(
            [
                '          ',
            ],
            $buffer->toLines(),
        );
    }

    private function render(Buffer $buffer, Widget $widget): void
    {
        $this->renderer()->render(
            new NullWidgetRenderer(),
            $widget,
            $buffer,
            $buffer->area(),
        );
    }

    private function renderer(): WidgetRenderer
    {
        $coreExtension = new CoreExtension();

        return new AggregateWidgetRenderer([
            new CanvasRenderer(
                new AggregateShapePainter($coreExtension->shapePainters()),
            ),
            new SubmitFieldWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
