<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Editor\LineEditor;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LineEditorWidget::class)]
#[Small]
final class LineEditorWidgetTest extends TestCase
{
    #[DataProvider('valueProvider')]
    #[TestDox('It has value: $scenario')]
    public function test_it_has_value(string $scenario, LineEditorWidget $widget, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $widget->value);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: LineEditorWidget,
     *     expectedValue: string,
     * }>
     */
    public static function valueProvider(): \Generator
    {
        yield [
            'scenario' => "empty by default (`''`)",
            'widget' => LineEditorWidget::empty(),
            'expectedValue' => '',
        ];
        yield [
            'scenario' => "that can be changed (e.g. `value('baldrick')`)",
            'widget' => LineEditorWidget::empty()->value('baldrick'),
            'expectedValue' => 'baldrick',
        ];
    }

    #[DataProvider('cursorPositionProvider')]
    #[TestDox('It has cursorPosition: $scenario')]
    public function test_it_has_cursor_position(string $scenario, LineEditorWidget $widget, int $expectedCursorPosition): void
    {
        $this->assertSame($expectedCursorPosition, $widget->cursorPosition);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: LineEditorWidget,
     *     expectedCursorPosition: int,
     * }>
     */
    public static function cursorPositionProvider(): \Generator
    {
        yield [
            'scenario' => '`0` by default',
            'widget' => LineEditorWidget::empty(),
            'expectedCursorPosition' => 0,
        ];
        yield [
            'scenario' => 'that can be moved (e.g. `cursorPosition(3)`)',
            'widget' => LineEditorWidget::empty()->value('baldrick')->cursorPosition(3),
            'expectedCursorPosition' => 3,
        ];
        yield [
            'scenario' => "moved to end when value is changed (e.g. `8` for `value('baldrick')`)",
            'widget' => LineEditorWidget::empty()->value('baldrick'),
            'expectedCursorPosition' => 8,
        ];
    }

    #[DataProvider('invalidCursorPositionProvider')]
    #[TestDox('It fails when cursorPosition is $scenario')]
    public function test_it_fails_with_invalid_cursor_position(string $scenario, LineEditorWidget $widget, int $cursorPosition): void
    {
        $this->expectException(ValidationFailedException::class);

        $widget->cursorPosition($cursorPosition);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: LineEditorWidget,
     *     cursorPosition: int,
     * }>
     */
    public static function invalidCursorPositionProvider(): \Generator
    {
        yield [
            'scenario' => 'negative (`-1` given)',
            'widget' => LineEditorWidget::empty(),
            'cursorPosition' => -1,
        ];
        yield [
            'scenario' => "beyond the value (`9` given for `'baldrick'`)",
            'widget' => LineEditorWidget::empty()->value('baldrick'),
            'cursorPosition' => 9,
        ];
    }

    #[DataProvider('focusedProvider')]
    #[TestDox('It has focused: $scenario')]
    public function test_it_has_focused_state(string $scenario, LineEditorWidget $widget, bool $expectedFocused): void
    {
        $this->assertSame($expectedFocused, $widget->focused);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     widget: LineEditorWidget,
     *     expectedFocused: bool,
     * }>
     */
    public static function focusedProvider(): \Generator
    {
        yield [
            'scenario' => '`false` by default',
            'widget' => LineEditorWidget::empty(),
            'expectedFocused' => false,
        ];
        yield [
            'scenario' => 'set to `true` with `focused()`',
            'widget' => LineEditorWidget::empty()->focused(),
            'expectedFocused' => true,
        ];
        yield [
            'scenario' => 'reset to `false` with `unfocused()`',
            'widget' => LineEditorWidget::empty()->focused()->unfocused(),
            'expectedFocused' => false,
        ];
    }
}
