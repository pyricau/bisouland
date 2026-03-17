<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\KeyHints;

use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidgetRenderer;
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

#[CoversClass(KeyHintsWidgetRenderer::class)]
#[Small]
final class KeyHintsWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('renderProvider')]
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
    public static function renderProvider(): \Generator
    {
        yield [
            'scenario' => 'empty   `` (no key hints)',
            'area' => Area::fromDimensions(5, 1),
            'widget' => KeyHintsWidget::from([]),
            'expected' => [
                '     ',
            ],
        ];
        yield [
            'scenario' => 'one as  `Quit:Esc`',
            'area' => Area::fromDimensions(8, 1),
            'widget' => KeyHintsWidget::from(['Quit' => 'Esc']),
            'expected' => [
                'Quit:Esc',
            ],
        ];
        yield [
            'scenario' => 'many as `Next:Tab | Back:Esc`',
            'area' => Area::fromDimensions(19, 1),
            'widget' => KeyHintsWidget::from(['Next' => 'Tab', 'Back' => 'Esc']),
            'expected' => [
                'Next:Tab | Back:Esc',
            ],
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
        $buffer = Buffer::empty(Area::fromDimensions(8, 1));
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
        // Renders: "Quit:Esc"
        $default = KeyHintsWidget::from(['Quit' => 'Esc']);

        yield [
            'scenario' => 'action in dark gray',
            'widget' => $default,
            'position' => Position::at(0, 0), // "Q" in "Quit:"
            'expectedFg' => AnsiColor::DarkGray,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'key in blue bold',
            'widget' => $default,
            'position' => Position::at(5, 0), // "E" in "Esc"
            'expectedFg' => AnsiColor::Blue,
            'expectedModifiers' => Modifier::BOLD,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));

        new KeyHintsWidgetRenderer()->render(
            new NullWidgetRenderer(),
            ParagraphWidget::fromString('Ignored'),
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(['          '], $buffer->toLines());
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
            new KeyHintsWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
