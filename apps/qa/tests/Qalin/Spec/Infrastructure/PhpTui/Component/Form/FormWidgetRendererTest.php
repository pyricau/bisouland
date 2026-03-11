<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form;

use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidgetRenderer;
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

#[CoversClass(FormWidgetRenderer::class)]
#[Small]
final class FormWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('formsProvider')]
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
    public static function formsProvider(): \Generator
    {
        yield [
            'scenario' => 'top    `╭Username────────╮` (InputField: rounded border with label)',
            'area' => Area::fromDimensions(18, 4),
            'widget' => FormWidget::fromItems(
                InputFieldWidget::fromLabel('Username'),
                SubmitFieldWidget::fromLabel('Submit'),
            ),
            'expected' => [
                '╭Username────────╮',
                '│                │',
                '╰────────────────╯',
                '[ Submit ]        ',
            ],
        ];
        yield [
            'scenario' => 'middle `│melchett        │` (InputField: content handled by LineEditorWidgetRenderer)',
            'area' => Area::fromDimensions(18, 4),
            'widget' => FormWidget::fromItems(
                InputFieldWidget::fromLabel('Username')->value('melchett'),
                SubmitFieldWidget::fromLabel('Submit'),
            ),
            'expected' => [
                '╭Username────────╮',
                '│melchett        │',
                '╰────────────────╯',
                '[ Submit ]        ',
            ],
        ];
        yield [
            'scenario' => 'bottom `╰────────────────╯` (InputField: rounded bottom border)',
            'area' => Area::fromDimensions(18, 4),
            'widget' => FormWidget::fromItems(
                InputFieldWidget::fromLabel('Username'),
                SubmitFieldWidget::fromLabel('Submit'),
            ),
            'expected' => [
                '╭Username────────╮',
                '│                │',
                '╰────────────────╯',
                '[ Submit ]        ',
            ],
        ];
        yield [
            'scenario' => 'submit `[ Submit ]        ` (SubmitField: label wrapped in brackets)',
            'area' => Area::fromDimensions(18, 4),
            'widget' => FormWidget::fromItems(
                InputFieldWidget::fromLabel('Username'),
                SubmitFieldWidget::fromLabel('Submit'),
            ),
            'expected' => [
                '╭Username────────╮',
                '│                │',
                '╰────────────────╯',
                '[ Submit ]        ',
            ],
        ];
        yield [
            'scenario' => 'choice `╭Era─────────────╮` (ChoiceFieldWidget: 3 + count(choices) rows)',
            'area' => Area::fromDimensions(18, 6),
            'widget' => FormWidget::fromItems(
                ChoiceFieldWidget::fromLabel('Era')
                    ->choices(['Medieval', 'Elizabethan'])
                    ->selectedIndex(0),
                SubmitFieldWidget::fromLabel('Submit'),
            ),
            'expected' => [
                '╭Era─────────────╮',
                '│                │',
                '│> Medieval      │',
                '│  Elizabethan   │',
                '╰────────────────╯',
                '[ Submit ]        ',
            ],
        ];
    }

    #[DataProvider('itemStylesProvider')]
    #[TestDox('It renders item style: $scenario')]
    public function test_it_renders_item_style(
        string $scenario,
        Widget $widget,
        Position $position,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(18, 4));
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
    public static function itemStylesProvider(): \Generator
    {
        // Form: InputField (rows 0–2) above SubmitField (row 3), area 18×4
        // InputField border: top-left corner at (0, 0)
        // SubmitField text: `[ Submit ]        ` → `[` at (0, 3)
        $focusedInputField = FormWidget::fromItems(
            InputFieldWidget::fromLabel('Username')->focused(),
            SubmitFieldWidget::fromLabel('Submit'),
        );
        $unfocusedInputField = FormWidget::fromItems(
            InputFieldWidget::fromLabel('Username'),
            SubmitFieldWidget::fromLabel('Submit'),
        );
        $focusedSubmitField = FormWidget::fromItems(
            InputFieldWidget::fromLabel('Username'),
            SubmitFieldWidget::fromLabel('Submit')->focused(),
        );

        yield [
            'scenario' => 'focused InputField border in yellow',
            'widget' => $focusedInputField,
            'position' => Position::at(0, 0),
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'unfocused InputField border in plain (terminal reset)',
            'widget' => $unfocusedInputField,
            'position' => Position::at(0, 0),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'focused SubmitField in yellow',
            'widget' => $focusedSubmitField,
            'position' => Position::at(0, 3),
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'unfocused SubmitField in plain (terminal reset)',
            'widget' => $unfocusedInputField,
            'position' => Position::at(0, 3),
            'expectedFg' => AnsiColor::Reset,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(18, 4));
        $widget = ParagraphWidget::fromString('Ignored');

        new FormWidgetRenderer()->render(
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
            new FormWidgetRenderer(),
            new ChoiceFieldWidgetRenderer(),
            new InputFieldWidgetRenderer(),
            new LineEditorWidgetRenderer(),
            new SubmitFieldWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
