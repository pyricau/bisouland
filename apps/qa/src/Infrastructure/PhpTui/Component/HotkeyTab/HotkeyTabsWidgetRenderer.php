<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders HotkeyTabsWidget as: `[1] TabA | [2] TabB` (or `[1] | [2]` when labels are empty).
 *
 * Registration:
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new HotkeyTabsWidgetRenderer())
 *         ->build();
 */
final class HotkeyTabsWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof HotkeyTabsWidget) {
            return;
        }

        $spans = [];
        $isFirstTab = true;
        foreach ($widget->hotkeyTabs as $key => $label) {
            $hotkey = (string) $key;
            $label = (string) $label;

            if (!$isFirstTab) {
                $spans[] = Span::styled(' | ', $widget->unfocusedLabelsStyle);
            }

            $isFirstTab = false;

            $spans[] = Span::styled('[', $widget->unfocusedLabelsStyle);
            $spans[] = Span::styled($hotkey, $widget->hotkeyStyle);
            $spans[] = Span::styled('' !== $label ? '] ' : ']', $widget->unfocusedLabelsStyle);
            $spans[] = Span::styled(
                $label,
                $hotkey === $widget->focusedHotkey
                    ? $widget->focusedLabelStyle
                    : $widget->unfocusedLabelsStyle,
            );
        }

        $renderer->render(
            $renderer,
            ParagraphWidget::fromLines(Line::fromSpans(...$spans)),
            $buffer,
            $area,
        );
    }
}
