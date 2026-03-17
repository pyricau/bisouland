<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\ChoiceField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChoiceFieldWidget::class)]
#[Small]
final class ChoiceFieldWidgetTest extends TestCase
{
    #[TestDox('It has label (e.g. `Era`)')]
    public function test_it_has_label(): void
    {
        $widget = ChoiceFieldWidget::fromLabel('Era');

        $this->assertSame('Era', $widget->label);
    }

    #[TestDox('It fails when label is empty (`""` given)')]
    public function test_it_fails_when_label_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        ChoiceFieldWidget::fromLabel('');
    }

    #[TestDox('It proxies focused()/unfocused() to lineEditorWidget')]
    public function test_it_proxies_focus_withers(): void
    {
        $widget = ChoiceFieldWidget::fromLabel('Era')->focused();
        $this->assertTrue($widget->lineEditorWidget->focused);
        $this->assertTrue($widget->focused);

        $widget = $widget->unfocused();
        $this->assertFalse($widget->lineEditorWidget->focused);
        $this->assertFalse($widget->focused);
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = ChoiceFieldWidget::fromLabel('Era');

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
            'scenario' => 'focusedBorder in yellow',
            'property' => 'focusedBorderStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Yellow),
        ];
        yield [
            'scenario' => 'unfocusedBorder in plain (terminal reset)',
            'property' => 'unfocusedBorderStyle',
            'expectedStyle' => Style::default(),
        ];
        yield [
            'scenario' => 'highlight reversed',
            'property' => 'highlightStyle',
            'expectedStyle' => Style::default()->addModifier(Modifier::REVERSED),
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

        $widget = ChoiceFieldWidget::fromLabel('Era')
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
            'scenario' => 'focusedBorder',
            'method' => 'focusedBorderStyle',
            'property' => 'focusedBorderStyle',
        ];
        yield [
            'scenario' => 'unfocusedBorder',
            'method' => 'unfocusedBorderStyle',
            'property' => 'unfocusedBorderStyle',
        ];
        yield [
            'scenario' => 'highlight',
            'method' => 'highlightStyle',
            'property' => 'highlightStyle',
        ];
    }
}
