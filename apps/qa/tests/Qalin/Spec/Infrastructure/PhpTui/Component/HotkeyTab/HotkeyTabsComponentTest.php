<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(HotkeyTabsComponent::class)]
#[Small]
final class HotkeyTabsComponentTest extends TestCase
{
    #[TestDox('It builds HotkeyTabsWidget snapshotting current tabs and focused hotkey')]
    public function test_it_builds_a_hotkey_tabs_widget_with_current_tabs_and_focus_on_frame_redraw(): void
    {
        $tabs = HotkeyTabsComponent::fromTabs(HotkeyFixtureTab::cases());
        $tab = HotkeyFixtureTab::TabB;
        $tabs->handle(CharKeyEvent::new($tab->key()));

        $widget = $tabs->build();

        $this->assertInstanceOf(HotkeyTabsWidget::class, $widget);
        $this->assertSame(['1' => 'TabA', '2' => 'TabB', '3' => 'TabC'], $widget->hotkeyTabs);
        $this->assertSame($tab->key(), $widget->focusedHotkey);
    }

    #[TestDox("It reports ComponentState::Changed when pressing another tab's hotkey")]
    public function test_it_reports_changed_when_pressing_another_tabs_hotkey(): void
    {
        $tabs = HotkeyTabsComponent::fromTabs(HotkeyFixtureTab::cases());
        $tab = HotkeyFixtureTab::TabB;

        $componentState = $tabs->handle(CharKeyEvent::new($tab->key()));

        $this->assertSame(ComponentState::Changed, $componentState);
        $this->assertSame($tab, $tabs->isFocused());
    }

    #[TestDox("It reports ComponentState::Handled when pressing the focused tab's hotkey")]
    public function test_it_reports_handled_when_pressing_the_focused_tabs_hotkey(): void
    {
        $tabs = HotkeyTabsComponent::fromTabs(HotkeyFixtureTab::cases());
        $tab = HotkeyFixtureTab::TabA;

        $componentState = $tabs->handle(CharKeyEvent::new($tab->key()));

        $this->assertSame(ComponentState::Handled, $componentState);
        $this->assertSame($tab, $tabs->isFocused());
    }

    #[DataProvider('ignoredEventsProvider')]
    #[TestDox('It reports ComponentState::Ignored when $scenario')]
    public function test_it_reports_ignored(string $scenario, Event $event): void
    {
        $tabs = HotkeyTabsComponent::fromTabs(HotkeyFixtureTab::cases());

        $componentState = $tabs->handle($event);

        $this->assertSame(ComponentState::Ignored, $componentState);
        $this->assertSame(HotkeyFixtureTab::TabA, $tabs->isFocused());
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     event: Event,
     * }>
     */
    public static function ignoredEventsProvider(): \Generator
    {
        yield [
            'scenario' => 'pressing an unregistered hotkey',
            'event' => CharKeyEvent::new('x'),
        ];
        yield [
            'scenario' => 'receiving a non CharKeyEvent (e.g. KeyCode::Tab)',
            'event' => CodedKeyEvent::new(KeyCode::Tab),
        ];
    }
}
