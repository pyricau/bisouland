<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\Constrained;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Layout\Constraint\LengthConstraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays horizontal hotkey tabs.
 *
 * Usage:
 *     // [hotkey => label, ...]
 *     $tabs = ['1' => 'TabA', '2' => 'TabB'];
 *
 *     // There are default styles, but can be customized
 *     $widget = HotkeyTabsWidget::fromTabs($tabs)
 *         ->hotkeyStyle($hotkeyStyle)
 *         ->focusedLabelStyle($focusedLabelStyle)
 *         ->unfocusedLabelsStyle($unfocusedLabelsStyle);
 *
 *     // Default focus is on first hotkey, use focus(hotkey) to switch
 *     $widget->focus('2');
 */
final readonly class HotkeyTabsWidget implements Widget, Constrained
{
    /**
     * @param non-empty-array<array-key, string> $hotkeyTabs hotkey => label
     *                                                       (array-key because PHP casts numeric string keys e.g. '1' to int)
     */
    private function __construct(
        public array $hotkeyTabs,
        public string $focusedHotkey,
        public Style $hotkeyStyle,
        public Style $focusedLabelStyle,
        public Style $unfocusedLabelsStyle,
    ) {
    }

    /**
     * @param array<array-key, string> $tabs hotkey => label
     *                                       (array-key because PHP casts numeric string keys e.g. '1' to int)
     */
    public static function fromTabs(array $tabs): self
    {
        if ([] === $tabs) {
            throw ValidationFailedException::make(
                'Invalid "HotkeyTabsWidget" parameter: tabs should not be empty (`[]` given)',
            );
        }

        foreach (array_keys($tabs) as $hotkey) {
            if (1 !== mb_strlen((string) $hotkey)) {
                throw ValidationFailedException::make(
                    "Invalid \"HotkeyTabsWidget\" parameter: tab hotkey should be a single character (`{$hotkey}` given)",
                );
            }
        }

        return new self(
            $tabs,
            (string) array_key_first($tabs), // Focus on first hotkey by default
            Style::default()->fg(AnsiColor::Blue)->addModifier(Modifier::BOLD),
            Style::default()->fg(AnsiColor::Yellow)->addModifier(Modifier::BOLD),
            Style::default()->fg(AnsiColor::DarkGray),
        );
    }

    public function focus(string $hotkey): self
    {
        if (!\array_key_exists($hotkey, $this->hotkeyTabs)) {
            throw ValidationFailedException::make(
                "Invalid \"HotkeyTabsWidget\" parameter: focusedHotkey should match an existing tab hotkey (`{$hotkey}` given)",
            );
        }

        return new self(
            $this->hotkeyTabs,
            $hotkey,
            $this->hotkeyStyle,
            $this->focusedLabelStyle,
            $this->unfocusedLabelsStyle,
        );
    }

    public function constraint(): LengthConstraint
    {
        return Constraint::length(3);
    }

    public function hotkeyStyle(Style $hotkeyStyle): self
    {
        return new self(
            $this->hotkeyTabs,
            $this->focusedHotkey,
            $hotkeyStyle,
            $this->focusedLabelStyle,
            $this->unfocusedLabelsStyle,
        );
    }

    public function focusedLabelStyle(Style $focusedLabelStyle): self
    {
        return new self(
            $this->hotkeyTabs,
            $this->focusedHotkey,
            $this->hotkeyStyle,
            $focusedLabelStyle,
            $this->unfocusedLabelsStyle,
        );
    }

    public function unfocusedLabelsStyle(Style $unfocusedLabelsStyle): self
    {
        return new self(
            $this->hotkeyTabs,
            $this->focusedHotkey,
            $this->hotkeyStyle,
            $this->focusedLabelStyle,
            $unfocusedLabelsStyle,
        );
    }
}
