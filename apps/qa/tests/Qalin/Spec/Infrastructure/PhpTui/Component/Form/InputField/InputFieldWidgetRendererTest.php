<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\InputField;

use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidgetRenderer;
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

#[CoversClass(InputFieldWidgetRenderer::class)]
#[Small]
final class InputFieldWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('fieldsProvider')]
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
    public static function fieldsProvider(): \Generator
    {
        yield [
            'scenario' => 'top    `╭Username────────╮` (rounded, with label)',
            'area' => Area::fromDimensions(18, 3),
            'widget' => InputFieldWidget::fromLabel('Username'),
            'expected' => [
                '╭Username────────╮',
                '│                │',
                '╰────────────────╯',
            ],
        ];
        yield [
            'scenario' => 'middle `│blackadder      │` (handled by LineEditorWidgetRenderer)',
            'area' => Area::fromDimensions(18, 3),
            'widget' => InputFieldWidget::fromLabel('Username')
                ->value('blackadder'),
            'expected' => [
                '╭Username────────╮',
                '│blackadder      │',
                '╰────────────────╯',
            ],
        ];
        yield [
            'scenario' => 'bottom `╰────────────────╯` (rounded)',
            'area' => Area::fromDimensions(18, 3),
            'widget' => InputFieldWidget::fromLabel('Username'),
            'expected' => [
                '╭Username────────╮',
                '│                │',
                '╰────────────────╯',
            ],
        ];
    }

    #[DataProvider('borderStylesProvider')]
    #[TestDox('It renders border style: $scenario')]
    public function test_it_renders_border_style(
        string $scenario,
        Widget $widget,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(18, 3));
        $this->render($buffer, $widget);

        $cell = $buffer->get(Position::at(0, 0)); // top-left border corner
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
    public static function borderStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'focusedBorder in yellow',
            'widget' => InputFieldWidget::fromLabel('Username')->focused(),
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'unfocusedBorder in plain (terminal reset)',
            'widget' => InputFieldWidget::fromLabel('Username'),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(18, 3));
        $widget = ParagraphWidget::fromString('Ignored');

        new InputFieldWidgetRenderer()->render(
            new NullWidgetRenderer(),
            $widget,
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(
            [
                '                  ',
                '                  ',
                '                  ',
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
            new InputFieldWidgetRenderer(),
            new LineEditorWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
