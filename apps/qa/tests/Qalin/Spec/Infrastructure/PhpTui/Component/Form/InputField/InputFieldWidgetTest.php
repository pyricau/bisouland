<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\InputField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputFieldWidget::class)]
#[Small]
final class InputFieldWidgetTest extends TestCase
{
    #[TestDox('It has label (e.g. `Username`)')]
    public function test_it_has_label(): void
    {
        $widget = InputFieldWidget::fromLabel('Username');

        $this->assertSame('Username', $widget->label);
    }

    #[TestDox('It fails when label is empty (`""` given)')]
    public function test_it_fails_when_label_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        InputFieldWidget::fromLabel('');
    }

    #[TestDox('It proxies value(), cursorPosition() and focused()/unfocused() to lineEditorWidget')]
    public function test_it_proxies_editor_withers(): void
    {
        $widget = InputFieldWidget::fromLabel('Username')
            ->value('blackadder')
            ->cursorPosition(3)
            ->focused();

        $this->assertSame('blackadder', $widget->lineEditorWidget->value);
        $this->assertSame(3, $widget->lineEditorWidget->cursorPosition);
        $this->assertTrue($widget->lineEditorWidget->focused);
        $this->assertFalse($widget->unfocused()->lineEditorWidget->focused);
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = InputFieldWidget::fromLabel('Username');

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
    }

    #[DataProvider('customStylesProvider')]
    #[TestDox('It can customize style: $scenario')]
    public function test_it_can_customize_style(
        string $scenario,
        string $method,
        string $property,
    ): void {
        $customStyle = Style::default()->fg(AnsiColor::Red);

        $widget = InputFieldWidget::fromLabel('Username')
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
    }
}
