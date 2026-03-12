<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\KeyValue;

use Bl\Qa\Infrastructure\PhpTui\Component\KeyValue\KeyValueWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyValue\KeyValueWidgetRenderer;
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

#[CoversClass(KeyValueWidgetRenderer::class)]
#[Small]
final class KeyValueWidgetRendererTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('rowsProvider')]
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
    public static function rowsProvider(): \Generator
    {
        yield [
            'scenario' => 'empty   `` (no rows)',
            'area' => Area::fromDimensions(5, 1),
            'widget' => KeyValueWidget::fromRows([]),
            'expected' => [
                '     ',
            ],
        ];
        yield [
            'scenario' => 'one as  `menu: rat au van`',
            'area' => Area::fromDimensions(16, 1),
            'widget' => KeyValueWidget::fromRows(['menu' => 'rat au van']),
            'expected' => [
                'menu: rat au van',
            ],
        ];
        yield [
            'scenario' => '1 of 2  `plan: rotten borough          ` (padded to width)',
            'area' => Area::fromDimensions(30, 2),
            'widget' => KeyValueWidget::fromRows(['plan' => 'rotten borough', 'lucky' => 'lucky us, cluck, cluck!']),
            'expected' => [
                'plan: rotten borough          ',
                'lucky: lucky us, cluck, cluck!',
            ],
        ];
        yield [
            'scenario' => '2 of 2  `lucky: lucky us, cluck, cluck!` (exact fit)',
            'area' => Area::fromDimensions(30, 2),
            'widget' => KeyValueWidget::fromRows(['plan' => 'rotten borough', 'lucky' => 'lucky us, cluck, cluck!']),
            'expected' => [
                'plan: rotten borough          ',
                'lucky: lucky us, cluck, cluck!',
            ],
        ];
        yield [
            'scenario' => 'wrap 1  `plan: so cunning` (key + start of value)',
            'area' => Area::fromDimensions(16, 3),
            'widget' => KeyValueWidget::fromRows(['plan' => 'so cunning you can brush your teeth with it']),
            'expected' => [
                'plan: so cunning',
                'you can brush   ',
                'your teeth with ',
            ],
        ];
        yield [
            'scenario' => 'wrap 2  `you can brush   ` (word-wrapped)',
            'area' => Area::fromDimensions(16, 3),
            'widget' => KeyValueWidget::fromRows(['plan' => 'so cunning you can brush your teeth with it']),
            'expected' => [
                'plan: so cunning',
                'you can brush   ',
                'your teeth with ',
            ],
        ];
        yield [
            'scenario' => 'wrap 3  `your teeth with ` (3-row area: `it` lost)',
            'area' => Area::fromDimensions(16, 3),
            'widget' => KeyValueWidget::fromRows(['plan' => 'so cunning you can brush your teeth with it']),
            'expected' => [
                'plan: so cunning',
                'you can brush   ',
                'your teeth with ',
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
        $buffer = Buffer::empty(Area::fromDimensions(4, 1));
        $this->render($buffer, $widget);

        $cell = $buffer->get(Position::at(0, 0));
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
            'scenario' => 'keyStyle in cyan',
            'widget' => KeyValueWidget::fromRows(['menu' => 'rat au van']),
            'expectedFg' => AnsiColor::Cyan,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 1));
        $widget = ParagraphWidget::fromString('Ignored');

        new KeyValueWidgetRenderer()->render(
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
            new KeyValueWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
