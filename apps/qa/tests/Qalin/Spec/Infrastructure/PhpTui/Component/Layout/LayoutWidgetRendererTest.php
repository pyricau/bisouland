<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Layout;

use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidgetRenderer;
use PhpTui\Tui\Canvas\AggregateShapePainter;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\CoreExtension;
use PhpTui\Tui\Extension\Core\Widget\CanvasRenderer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\AggregateWidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\NullWidgetRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutWidgetRenderer::class)]
#[Small]
final class LayoutWidgetRendererTest extends TestCase
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
        // Area: 10 wide × 13 tall
        // Banner  (BannerWidget::from(['≋'])):             rows  0–2  (constraint: length(3) = 1 logo + 2 borders)
        // Navbar  (HotkeyTabsWidget::fromTabs(['1'=>'N'])): rows  3–5  (constraint: length(3) = 1 row  + 2 borders)
        // Body    (ParagraphWidget 'Body'):                 rows  6–9  (min(0) → 4 rows = 2 borders + 1 content + 1 bottom pad)
        // Footer  (KeyHintsWidget::from(['Quit'=>'Esc'])): rows 10–12 (constraint: length(3) = 1 row  + 2 borders)
        $area = Area::fromDimensions(10, 13);
        $widget = LayoutWidget::from(
            BannerWidget::from(['≋']),
            HotkeyTabsWidget::fromTabs(['1' => 'N']),
            ParagraphWidget::fromString('Body'),
            KeyHintsWidget::from(['Quit' => 'Esc']),
        );
        $expected = [
            '╭────────╮',
            '│≋       │',
            '╰────────╯',
            '╭────────╮',
            '│[1] N   │',
            '╰────────╯',
            '╭────────╮',
            '│ Body   │',
            '│        │',
            '╰────────╯',
            '╭────────╮',
            '│Quit:Esc│',
            '╰────────╯',
        ];

        yield ['scenario' => 'banner top border    `╭────────╮`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'banner logo line     `│≋       │`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'banner bottom border `╰────────╯`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'navbar top border    `╭────────╮`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'navbar tab row       `│[1] N   │`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'navbar bottom border `╰────────╯`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'body top border      `╭────────╮`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'body content row     `│ Body   │`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'body padding row     `│        │`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'body bottom border   `╰────────╯`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'footer top border    `╭────────╮`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'footer hints row     `│Quit:Esc│`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
        yield ['scenario' => 'footer bottom border `╰────────╯`', 'area' => $area, 'widget' => $widget, 'expected' => $expected];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(10, 13));

        new LayoutWidgetRenderer()->render(
            new NullWidgetRenderer(),
            ParagraphWidget::fromString('Ignored'),
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(
            [
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
                '          ',
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
            new LayoutWidgetRenderer(),
            new BannerWidgetRenderer(),
            new HotkeyTabsWidgetRenderer(),
            new KeyHintsWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
