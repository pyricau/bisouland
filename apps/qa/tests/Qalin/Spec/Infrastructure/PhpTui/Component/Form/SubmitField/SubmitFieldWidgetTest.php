<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\SubmitField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubmitFieldWidget::class)]
#[Small]
final class SubmitFieldWidgetTest extends TestCase
{
    #[TestDox('It has label (e.g. `Submit`)')]
    public function test_it_has_label(): void
    {
        $widget = SubmitFieldWidget::fromLabel('Submit');

        $this->assertSame('Submit', $widget->label);
    }

    #[TestDox('It fails when label is empty (`""` given)')]
    public function test_it_fails_when_label_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        SubmitFieldWidget::fromLabel('');
    }

    #[DataProvider('focusedProvider')]
    #[TestDox('It has focused: $scenario')]
    public function test_it_has_focused(
        string $scenario,
        bool $expected,
        SubmitFieldWidget $widget,
    ): void {
        $this->assertSame($expected, $widget->focused);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     expected: bool,
     *     widget: SubmitFieldWidget,
     * }>
     */
    public static function focusedProvider(): \Generator
    {
        yield [
            'scenario' => '`false` by default',
            'expected' => false,
            'widget' => SubmitFieldWidget::fromLabel('Submit'),
        ];
        yield [
            'scenario' => 'set to `true` with `focused()`',
            'expected' => true,
            'widget' => SubmitFieldWidget::fromLabel('Submit')->focused(),
        ];
        yield [
            'scenario' => 'reset to `false` with `unfocused()`',
            'expected' => false,
            'widget' => SubmitFieldWidget::fromLabel('Submit')->focused()->unfocused(),
        ];
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = SubmitFieldWidget::fromLabel('Submit');

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
            'scenario' => 'focusedStyle in yellow',
            'property' => 'focusedStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Yellow),
        ];
        yield [
            'scenario' => 'unfocusedStyle in plain (terminal reset)',
            'property' => 'unfocusedStyle',
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

        $widget = SubmitFieldWidget::fromLabel('Submit')
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
            'scenario' => 'focusedStyle',
            'method' => 'focusedStyle',
            'property' => 'focusedStyle',
        ];
        yield [
            'scenario' => 'unfocusedStyle',
            'method' => 'unfocusedStyle',
            'property' => 'unfocusedStyle',
        ];
    }
}
