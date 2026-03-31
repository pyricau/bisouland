<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Banner;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(BannerWidget::class)]
#[Small]
final class BannerWidgetTest extends TestCase
{
    /** @param list<string> $logo */
    #[DataProvider('logoProvider')]
    #[TestDox('It has logo: $scenario')]
    public function test_it_has_logo(
        string $scenario,
        array $logo,
    ): void {
        $widget = BannerWidget::from($logo);
        $this->assertSame($logo, $widget->logo);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     logo: list<string>,
     * }>
     */
    public static function logoProvider(): \Generator
    {
        yield [
            'scenario' => 'empty (`[]`)',
            'logo' => [],
        ];
        yield [
            'scenario' => 'one as `["≋"]` (1 string, 1 char)',
            'logo' => ['≋'],
        ];
        yield [
            'scenario' => 'many as `[top, middle, bottom]` (3 strings, 5 chars each)',
            'logo' => [
                '██▀▀▄', // top
                '██▄▄▀', // middle
                '██▀▀▄', // bottom
            ],
        ];
    }

    /** @param list<string> $logo */
    #[DataProvider('constraintProvider')]
    #[TestDox('It has constraint: $scenario')]
    public function test_it_has_constraint(
        string $scenario,
        array $logo,
        Constraint $expected,
    ): void {
        $this->assertEquals($expected, BannerWidget::from($logo)->constraint());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     logo: list<string>,
     *     expected: Constraint,
     * }>
     */
    public static function constraintProvider(): \Generator
    {
        yield [
            'scenario' => 'empty logo (0 lines) → length(2)',
            'logo' => [],
            'expected' => Constraint::length(2),
        ];
        yield [
            'scenario' => '1-line logo → length(3)',
            'logo' => ['≋'],
            'expected' => Constraint::length(3),
        ];
        yield [
            'scenario' => '3-line logo → length(5)',
            'logo' => ['██▀▀▄', '██▄▄▀', '██▀▀▄'],
            'expected' => Constraint::length(5),
        ];
    }

    /** @param list<string> $logo */
    #[DataProvider('invalidLogoProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_with_invalid_logo(
        string $scenario,
        array $logo,
    ): void {
        $this->expectException(ValidationFailedException::class);
        BannerWidget::from($logo);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     logo: list<string>,
     * }>
     */
    public static function invalidLogoProvider(): \Generator
    {
        yield [
            'scenario' => "logo strings have different lengths (`['xx', 'y']` given)",
            'logo' => ['xx', 'y'],
        ];
    }

    /** @param list<string> $slogan */
    #[DataProvider('sloganProvider')]
    #[TestDox('It has slogan: $scenario')]
    public function test_it_has_slogan(
        string $scenario,
        array $slogan,
    ): void {
        $widget = BannerWidget::from([], ...$slogan);
        $this->assertSame($slogan, $widget->slogan);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     slogan: list<string>,
     * }>
     */
    public static function sloganProvider(): \Generator
    {
        yield [
            'scenario' => 'empty (`[]`)',
            'slogan' => [],
        ];
        yield [
            'scenario' => 'one as `["Rollercoaster of emotions"]`',
            'slogan' => ['Rollercoaster of emotions'],
        ];
        yield [
            'scenario' => 'many as `["We\'re in the stickiest situation", "since Sticky the Stick Insect", "got stuck on a sticky bun"]`',
            'slogan' => [
                "We're in the stickiest situation",
                'since Sticky the Stick Insect',
                'got stuck on a sticky bun',
            ],
        ];
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = BannerWidget::from([]);

        $this->assertEquals($expectedStyle, $widget->$property);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     property: string,
     *     expectedStyle: Style,
     * }>
     */
    public static function defaultStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'logo in yellow',
            'property' => 'logoStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Yellow),
        ];
        yield [
            'scenario' => 'slogan in plain (terminal default)',
            'property' => 'sloganStyle',
            'expectedStyle' => Style::default(),
        ];
    }

    #[DataProvider('customStylesProvider')]
    #[TestDox('It can customize style: $scenario')]
    public function test_it_can_customize_style(
        string $scenario,
        string $method,
        string $property,
    ): void {
        $customStyle = Style::default()->fg(AnsiColor::Red);

        $widget = BannerWidget::from([])
            ->$method($customStyle);

        $this->assertSame($customStyle, $widget->$property);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     method: string,
     *     property: string,
     * }>
     */
    public static function customStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'logo',
            'method' => 'logoStyle',
            'property' => 'logoStyle',
        ];
        yield [
            'scenario' => 'slogan',
            'method' => 'sloganStyle',
            'property' => 'sloganStyle',
        ];
    }
}
