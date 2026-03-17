<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\KeyHints;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(KeyHintsWidget::class)]
#[Small]
final class KeyHintsWidgetTest extends TestCase
{
    /** @param array<string, string> $keyHints */
    #[DataProvider('keyHintsProvider')]
    #[TestDox('It has keyHints: $scenario')]
    public function test_it_has_key_hints(string $scenario, array $keyHints): void
    {
        $widget = KeyHintsWidget::from($keyHints);

        $this->assertSame($keyHints, $widget->keyHints);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     keyHints: array<string, string>,
     * }>
     */
    public static function keyHintsProvider(): \Generator
    {
        yield [
            'scenario' => 'empty (`[]`)',
            'keyHints' => [],
        ];
        yield [
            'scenario' => "one as `['Quit' => 'Esc']` (`[action => key]`)",
            'keyHints' => ['Quit' => 'Esc'],
        ];
        yield [
            'scenario' => "many as `['Next' => 'Tab', 'Select' => 'Enter', 'Back' => 'Esc']` (`[action => key]`)",
            'keyHints' => ['Next' => 'Tab', 'Select' => 'Enter', 'Back' => 'Esc'],
        ];
    }

    #[TestDox('It has constraint (e.g. Constraint::length(3): 1 content row + 2 border rows)')]
    public function test_it_has_constraint(): void
    {
        $this->assertEquals(Constraint::length(3), KeyHintsWidget::from(['Quit' => 'Esc'])->constraint());
    }

    /** @param array<string, string> $keyHints */
    #[DataProvider('invalidKeyHintsProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_with_invalid_key_hints(string $scenario, array $keyHints): void
    {
        $this->expectException(ValidationFailedException::class);

        KeyHintsWidget::from($keyHints);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     keyHints: array<string, string>,
     * }>
     */
    public static function invalidKeyHintsProvider(): \Generator
    {
        yield [
            'scenario' => "action is empty (`['' => 'Esc']` given)",
            'keyHints' => ['' => 'Esc'],
        ];
        yield [
            'scenario' => "key is empty (`['Quit' => '']` given)",
            'keyHints' => ['Quit' => ''],
        ];
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = KeyHintsWidget::from(['Quit' => 'Esc']);

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
            'scenario' => 'action in dark gray',
            'property' => 'actionStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::DarkGray),
        ];
        yield [
            'scenario' => 'key in blue bold',
            'property' => 'keyStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Blue)->addModifier(Modifier::BOLD),
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

        $widget = KeyHintsWidget::from(['Quit' => 'Esc'])
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
            'scenario' => 'action',
            'method' => 'actionStyle',
            'property' => 'actionStyle',
        ];
        yield [
            'scenario' => 'key',
            'method' => 'keyStyle',
            'property' => 'keyStyle',
        ];
    }
}
