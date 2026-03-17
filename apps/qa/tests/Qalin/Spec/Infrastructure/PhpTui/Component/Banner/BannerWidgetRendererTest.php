<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Banner;

use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidgetRenderer;
use Bl\Qa\UserInterface\Tui\QalinBanner;
use PhpTui\Tui\Canvas\AggregateShapePainter;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\CoreExtension;
use PhpTui\Tui\Extension\Core\Widget\CanvasRenderer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Position\Position;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\AggregateWidgetRenderer;
use PhpTui\Tui\Widget\WidgetRenderer\NullWidgetRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(BannerWidgetRenderer::class)]
#[Small]
final class BannerWidgetRendererTest extends TestCase
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
            'scenario' => 'empty',
            'area' => Area::fromDimensions(5, 1),
            'widget' => BannerWidget::from([]),
            'expected' => ['     '],
        ];

        yield [
            'scenario' => 'no logo, slogan `   Rollercoaster of emotions`',
            'area' => Area::fromDimensions(28, 1),
            'widget' => BannerWidget::from([], 'Rollercoaster of emotions'),
            'expected' => ['   Rollercoaster of emotions'],
        ];

        $logo = ['██▀▀▄', '██▄▄▀', '██▀▀▄'];

        $logoNoSlogan = BannerWidget::from($logo);
        $logoNoSloganArea = Area::fromDimensions(5, 3);
        $logoNoSloganExpected = [
            '██▀▀▄',
            '██▄▄▀',
            '██▀▀▄',
        ];

        yield [
            'scenario' => 'logo, no slogan top    `██▀▀▄`',
            'area' => $logoNoSloganArea,
            'widget' => $logoNoSlogan,
            'expected' => $logoNoSloganExpected,
        ];
        yield [
            'scenario' => 'logo, no slogan middle `██▄▄▀`',
            'area' => $logoNoSloganArea,
            'widget' => $logoNoSlogan,
            'expected' => $logoNoSloganExpected,
        ];
        yield [
            'scenario' => 'logo, no slogan bottom `██▀▀▄`',
            'area' => $logoNoSloganArea,
            'widget' => $logoNoSlogan,
            'expected' => $logoNoSloganExpected,
        ];

        $logoShorterSlogan = BannerWidget::from($logo, 'My Magnum Opus', 'My magnificient Octopus');
        $logoShorterSloganArea = Area::fromDimensions(31, 3);
        $logoShorterSloganExpected = [
            '██▀▀▄   My Magnum Opus         ',
            '██▄▄▀   My magnificient Octopus',
            '██▀▀▄                          ',
        ];

        yield [
            'scenario' => 'logo, shorter slogan top    `██▀▀▄   My Magnum Opus         `',
            'area' => $logoShorterSloganArea,
            'widget' => $logoShorterSlogan,
            'expected' => $logoShorterSloganExpected,
        ];
        yield [
            'scenario' => 'logo, shorter slogan middle `██▄▄▀   My magnificient Octopus`',
            'area' => $logoShorterSloganArea,
            'widget' => $logoShorterSlogan,
            'expected' => $logoShorterSloganExpected,
        ];
        yield [
            'scenario' => 'logo, shorter slogan bottom `██▀▀▄                          `',
            'area' => $logoShorterSloganArea,
            'widget' => $logoShorterSlogan,
            'expected' => $logoShorterSloganExpected,
        ];

        $logoLongerSlogan = BannerWidget::from(
            ['≋'],
            "We're in the stickiest situation",
            'since Sticky the Stick Insect',
            'got stuck on a sticky bun',
        );
        $logoLongerSloganArea = Area::fromDimensions(36, 3);
        $logoLongerSloganExpected = [
            "≋   We're in the stickiest situation",
            '    since Sticky the Stick Insect   ',
            '    got stuck on a sticky bun       ',
        ];

        yield [
            'scenario' => 'logo, longer slogan top    `≋   We\'re in the stickiest situation`',
            'area' => $logoLongerSloganArea,
            'widget' => $logoLongerSlogan,
            'expected' => $logoLongerSloganExpected,
        ];
        yield [
            'scenario' => 'logo, longer slogan middle `    since Sticky the Stick Insect   `',
            'area' => $logoLongerSloganArea,
            'widget' => $logoLongerSlogan,
            'expected' => $logoLongerSloganExpected,
        ];
        yield [
            'scenario' => 'logo, longer slogan bottom `    got stuck on a sticky bun       `',
            'area' => $logoLongerSloganArea,
            'widget' => $logoLongerSlogan,
            'expected' => $logoLongerSloganExpected,
        ];
    }

    #[DataProvider('logoStylesProvider')]
    #[TestDox('It renders logo style: $scenario')]
    public function test_it_renders_logo_style(
        string $scenario,
        Widget $widget,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(18, 6));
        $this->render($buffer, $widget);

        $cell = $buffer->get(Position::at(2, 0)); // first '█' in '  ████      ████  '
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
    public static function logoStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'logo in yellow',
            'widget' => BannerWidget::from(QalinBanner::LOGO),
            'expectedFg' => AnsiColor::Yellow,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    #[DataProvider('sloganStylesProvider')]
    #[TestDox('It renders slogan style: $scenario')]
    public function test_it_renders_slogan_style(
        string $scenario,
        Widget $widget,
        AnsiColor $expectedFg,
        int $expectedModifiers,
    ): void {
        $buffer = Buffer::empty(Area::fromDimensions(27, 6));
        $this->render($buffer, $widget);

        $cell = $buffer->get(Position::at(21, 0)); // 'R' in 'Rollercoaster of emotions': col 18 (slogan start) + 3 (gap)
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
    public static function sloganStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'slogan in cyan',
            'widget' => BannerWidget::from(QalinBanner::LOGO, 'Rollercoaster of emotions')
                ->sloganStyle(Style::default()->fg(AnsiColor::Cyan)),
            'expectedFg' => AnsiColor::Cyan,
            'expectedModifiers' => Modifier::NONE,
        ];
    }

    public function test_it_ignores_unsupported_widgets(): void
    {
        $buffer = Buffer::empty(Area::fromDimensions(18, 6));

        new BannerWidgetRenderer()->render(
            new NullWidgetRenderer(),
            ParagraphWidget::fromString('Ignored'),
            $buffer,
            $buffer->area(),
        );

        $this->assertSame(
            [
                '                  ',
                '                  ',
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
            new BannerWidgetRenderer(),
            ...$coreExtension->widgetRenderers(),
        ]);
    }
}
