<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Qa\Infrastructure\PhpTui\Component;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;

/**
 * Handles HotkeyTabsWidget events (is registered hotkey pressed?) and builds it on frame redraw.
 *
 * @template TTab of HotkeyTab
 *
 * Usage:
 *     enum MyTab: string implements HotkeyTab
 *     {
 *         case TabA = 'TabA';
 *         case TabB = 'TabB';
 *
 *         public function key(): string
 *         {
 *             return match ($this) {
 *                 self::TabA => '1',
 *                 self::TabB => '2',
 *             };
 *         }
 *
 *         public function label(): string
 *         {
 *             return $this->value;
 *         }
 *     }
 *
 *     $tabs = HotkeyTabsComponent::fromTabs(MyTab::cases());
 *     $tabs->build();         // returns HotkeyTabsWidget for rendering
 *     $tabs->handle($event);  // focuses a tab on hotkey press
 *     $tabs->isFocused();     // returns the currently focused tab value
 */
final class HotkeyTabsComponent implements Component
{
    private int $focusedIndex = 0;

    /**
     * @param non-empty-list<TTab> $tabs
     */
    private function __construct(
        private readonly array $tabs,
    ) {
    }

    /**
     * @param non-empty-list<TTab> $tabs
     *
     * @return self<TTab>
     */
    public static function fromTabs(array $tabs): self
    {
        return new self($tabs);
    }

    public function handle(Event $event): ComponentState
    {
        if (!$event instanceof CharKeyEvent) {
            return ComponentState::Ignored;
        }

        foreach ($this->tabs as $index => $tab) {
            if ($event->char === $tab->key()) {
                if ($index === $this->focusedIndex) {
                    return ComponentState::Handled;
                }

                $this->focusedIndex = $index;

                return ComponentState::Changed;
            }
        }

        return ComponentState::Ignored;
    }

    public function build(): HotkeyTabsWidget
    {
        $tabs = [];
        foreach ($this->tabs as $tab) {
            $tabs[$tab->key()] = $tab->label();
        }

        return HotkeyTabsWidget::fromTabs($tabs)
            ->focus($this->tabs[$this->focusedIndex]->key());
    }

    /**
     * @return TTab
     */
    public function isFocused(): mixed
    {
        return $this->tabs[$this->focusedIndex];
    }
}
