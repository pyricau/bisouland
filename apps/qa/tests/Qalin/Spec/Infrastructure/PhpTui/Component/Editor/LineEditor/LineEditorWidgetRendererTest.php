<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Editor\LineEditor;

use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidgetRenderer;
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

#[CoversClass(LineEditorWidgetRenderer::class)]
#[Small]
final class LineEditorWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('renderProvider')]
    #[TestDox('It renders: $scenario')]
    public function test_it_renders(string $scenario, Area $area, Widget $widget, array $expected): void
    {
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
    public static function renderProvider(): \Generator
    {
        yield [
            'scenario' => 'unfocused, empty as `                `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty(),
            'expected' => ['                '],
        ];
        yield [
            'scenario' => 'unfocused, with value as `baldrick        `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty()->value('baldrick'),
            'expected' => ['baldrick        '],
        ];
        yield [
            'scenario' => 'focused, empty as `█               `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty()->focused(),
            'expected' => ['                '],
        ];
        yield [
            'scenario' => 'focused, with value, cursor at the end as `baldrick█       `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty()->value('baldrick')->focused(),
            'expected' => ['baldrick        '],
        ];
        yield [
            'scenario' => 'focused, with value, cursor in the middle as `bald█ick        `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty()->value('baldrick')->cursorPosition(4)->focused(),
            'expected' => ['baldrick        '],
        ];
        yield [
            'scenario' => 'focused, with value, cursor at the start as `█aldrick        `',
            'area' => Area::fromDimensions(16, 1),
            'widget' => LineEditorWidget::empty()->value('baldrick')->cursorPosition(0)->focused(),
            'expected' => ['baldrick        '],
        ];
    }

    #[DataProvider('stylesProvider')]
    #[TestDox('It renders style: $scenario')]
    public function test_it_renders_style(
        string $scenario,
        Widget $widget,
        Position $position,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(16, 1));
        $this->render($buffer, $widget);

        $cell = $buffer->get($position);
        $this->assertSame($expectedFg, $cell->fg);
        $this->assertSame($expectedModifiers, $cell->modifiers);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: Widget,
     *     position: Position,
     *     expectedFg: AnsiColor,
     *     expectedModifiers: int,
     * }>
     */
    public static function stylesProvider(): \Generator
    {
        $focused = LineEditorWidget::empty()->value('baldrick')->focused();
        $unfocused = LineEditorWidget::empty()->value('baldrick');

        yield [
            'scenario' => 'cursor, focused, past end of value: space in "reversed" (`█`)',
            'widget' => $focused,
            'position' => Position::at(8, 0),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::REVERSED,
        ];
        yield [
            'scenario' => 'cursor, focused, within value: char in "reversed"',
            'widget' => LineEditorWidget::empty()->value('baldrick')->cursorPosition(0)->focused(),
            'position' => Position::at(0, 0),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::REVERSED,
        ];
        yield [
            'scenario' => 'cursor, unfocused in "none" (not displayed)',
            'widget' => $unfocused,
            'position' => Position::at(0, 0),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(16, 1));
        $widget = ParagraphWidget::fromString('Ignored');
        $lineEditorWidgetRenderer = new LineEditorWidgetRenderer();

        $lineEditorWidgetRenderer->render(
            new NullWidgetRenderer(),
            $widget,
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(['                '], $buffer->toLines());
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
            new LineEditorWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
