<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(HotkeyTabsWidget::class)]
#[Small]
final class HotkeyTabsWidgetTest extends TestCase
{
    /** @param array<array-key, string> $tabs */
    #[DataProvider('tabsProvider')]
    #[TestDox('It has hotkeyTabs: $scenario')]
    public function test_it_has_hotkey_tabs(
        string $scenario,
        array $tabs,
    ): void {
        $tabsWidget = HotkeyTabsWidget::fromTabs($tabs);

        $this->assertSame($tabs, $tabsWidget->hotkeyTabs);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     tabs: array<array-key, string>
     * }>
     */
    public static function tabsProvider(): \Generator
    {
        yield [
            'scenario' => "one as `['1' => 'TabA']` (`[hotkey => label]`)",
            'tabs' => ['1' => 'TabA'],
        ];
        yield [
            'scenario' => "many as `['1' => 'TabA', '2' => 'TabB', '3' => 'TabC']` (`[hotkey => label]`)",
            'tabs' => ['1' => 'TabA', '2' => 'TabB', '3' => 'TabC'],
        ];
        yield [
            'scenario' => "with empty label (e.g. `['1' => '']`)",
            'tabs' => ['1' => ''],
        ];
    }

    #[TestDox('It has constraint (e.g. Constraint::length(3): 1 content row + 2 border rows)')]
    public function test_it_has_constraint(): void
    {
        $this->assertEquals(Constraint::length(3), HotkeyTabsWidget::fromTabs(['1' => 'TabA'])->constraint());
    }

    /** @param array<array-key, string> $tabs */
    #[DataProvider('invalidTabsProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_with_invalid_tabs(
        string $scenario,
        array $tabs,
    ): void {
        $this->expectException(ValidationFailedException::class);

        HotkeyTabsWidget::fromTabs($tabs);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     tabs: array<array-key, string>,
     * }>
     */
    public static function invalidTabsProvider(): \Generator
    {
        yield [
            'scenario' => 'hotkeyTabs is empty (`[]` given)',
            'tabs' => [],
        ];
        yield [
            'scenario' => "hotkey is missing (`['TabA']` given)",
            'tabs' => ['' => 'TabA'],
        ];
        yield [
            'scenario' => "hotkey is empty (`['' => 'TabA']` given)",
            'tabs' => ['' => 'TabA'],
        ];
        yield [
            'scenario' => "hotkey is more than one character (`['ab' => 'TabA']` given)",
            'tabs' => ['ab' => 'TabA'],
        ];
    }

    #[DataProvider('focusProvider')]
    #[TestDox('It has focusedHotkey: $scenario')]
    public function test_it_has_focused_hotkey(
        string $scenario,
        string $expectedHotkey,
        ?string $hotkey,
    ): void {
        $tabs = ['1' => 'TabA', '2' => 'TabB', '3' => 'TabC'];
        $widget = HotkeyTabsWidget::fromTabs($tabs);
        if (null !== $hotkey) {
            $widget = $widget->focus($hotkey);
        }

        $this->assertSame($expectedHotkey, $widget->focusedHotkey);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     expectedHotkey: string,
     *     hotkey: ?string,
     * }>
     */
    public static function focusProvider(): \Generator
    {
        yield [
            'scenario' => 'first one by default (e.g. `1` for `TabA`)',
            'expectedHotkey' => '1',
            'hotkey' => null,
        ];
        yield [
            'scenario' => "can switch to another one (e.g. `focus('2')` for `TabB`)",
            'expectedHotkey' => '2',
            'hotkey' => '2',
        ];
    }

    #[TestDox("It fails when focusing on non existing hotkey (e.g. `focus('4')`)")]
    public function test_it_fails_when_focusing_on_a_non_existing_hotkey(): void
    {
        $this->expectException(ValidationFailedException::class);

        HotkeyTabsWidget::fromTabs(['1' => 'TabA', '2' => 'TabB'])
            ->focus('3');
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = HotkeyTabsWidget::fromTabs(['1' => 'TabA']);

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
            'scenario' => 'hotkey in blue bold',
            'property' => 'hotkeyStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Blue)->addModifier(Modifier::BOLD),
        ];
        yield [
            'scenario' => 'focusedLabel in yellow bold',
            'property' => 'focusedLabelStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Yellow)->addModifier(Modifier::BOLD),
        ];
        yield [
            'scenario' => 'unfocusedLabels in dark gray',
            'property' => 'unfocusedLabelsStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::DarkGray),
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

        $widget = HotkeyTabsWidget::fromTabs(['1' => 'TabA'])
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
            'scenario' => 'hotkey',
            'method' => 'hotkeyStyle',
            'property' => 'hotkeyStyle',
        ];
        yield [
            'scenario' => 'focusedLabel',
            'method' => 'focusedLabelStyle',
            'property' => 'focusedLabelStyle',
        ];
        yield [
            'scenario' => 'unfocusedLabels',
            'method' => 'unfocusedLabelsStyle',
            'property' => 'unfocusedLabelsStyle',
        ];
    }
}
