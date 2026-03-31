<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidgetRenderer;
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

#[CoversClass(HotkeyTabsWidgetRenderer::class)]
#[Small]
final class HotkeyTabsWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('tabsProvider')]
    #[TestDox('It renders hotkeyTabs: $scenario')]
    public function test_it_renders_hotkey_tabs(
        string $scenario,
        Widget $widget,
        array $expected,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(32, 2));
        $this->render($buffer, $widget);
        $this->assertSame($expected, $buffer->toLines());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: Widget,
     *     expected: list<string>,
     * }>
     */
    public static function tabsProvider(): \Generator
    {
        yield [
            'scenario' => 'one as `[1] TabA`',
            'widget' => HotkeyTabsWidget::fromTabs([
                '1' => 'TabA',
            ])->focus('1'),
            'expected' => [
                '[1] TabA                        ',
                '                                ',
            ],
        ];
        yield [
            'scenario' => 'many as `[1] TabA | [2] TabB | [3] TabC`',
            'widget' => HotkeyTabsWidget::fromTabs([
                '1' => 'TabA',
                '2' => 'TabB',
                '3' => 'TabC',
            ])->focus('1'),
            'expected' => [
                '[1] TabA | [2] TabB | [3] TabC  ',
                '                                ',
            ],
        ];
        yield [
            'scenario' => 'with empty label as `[1]`',
            'widget' => HotkeyTabsWidget::fromTabs([
                '1' => '',
            ]),
            'expected' => [
                '[1]                             ',
                '                                ',
            ],
        ];
        yield [
            'scenario' => 'with many empty labels as `[1] | [2]`',
            'widget' => HotkeyTabsWidget::fromTabs([
                '1' => '',
                '2' => '',
            ]),
            'expected' => [
                '[1] | [2]                       ',
                '                                ',
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
        $buffer = Buffer::empty(Area::fromDimensions(32, 1));
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
        // Renders: "[1] TabA | [2] TabB"
        $focusedOnFirst = HotkeyTabsWidget::fromTabs([
            '1' => 'TabA',
            '2' => 'TabB',
        ])->focus('1');
        $focusedOnSecond = HotkeyTabsWidget::fromTabs([
            '1' => 'TabA',
            '2' => 'TabB',
        ])->focus('2');

        yield [
            'scenario' => 'hotkey in blue bold',
            'widget' => $focusedOnFirst,
            'position' => Position::at(1, 0), // "1" in "[1] TabA"
            'expectedFg' => AnsiColor::Blue,
            'expectedModifiers' => Modifier::BOLD,
        ];
        yield [
            'scenario' => 'focusedLabel in yellow bold',
            'widget' => $focusedOnFirst,
            'position' => Position::at(4, 0), // "T" in "TabA" (focused)
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::BOLD,
        ];
        yield [
            'scenario' => 'unfocusedLabels in plain dark gray',
            'widget' => $focusedOnFirst,
            'position' => Position::at(15, 0), // "T" in "TabB" (not focused)
            'expectedFg' => AnsiColor::DarkGray,
            'expectedModifiers' => Modifier::NONE,
        ];
        yield [
            'scenario' => 'focusedLabel after switching in yellow bold',
            'widget' => $focusedOnSecond,
            'position' => Position::at(15, 0), // "T" in "TabB" (now focused)
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::BOLD,
        ];
        yield [
            'scenario' => 'unfocusedLabels after switching in plain dark gray',
            'widget' => $focusedOnSecond,
            'position' => Position::at(4, 0), // "T" in "TabA" (no longer focused)
            'expectedFg' => AnsiColor::DarkGray,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(32, 1));
        $widget = ParagraphWidget::fromString('Ignored');

        new HotkeyTabsWidgetRenderer()->render(
            new NullWidgetRenderer(),
            $widget,
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(
            [
                '                                ',
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
            new HotkeyTabsWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
