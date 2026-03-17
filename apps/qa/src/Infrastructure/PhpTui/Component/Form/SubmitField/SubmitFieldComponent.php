<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField;

use Bl\Qa\Infrastructure\PhpTui\Component\Form\Field;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;

/**
 * Handles Enter key press and builds a SubmitFieldWidget on frame redraw.
 *
 * Usage:
 *     $button = SubmitFieldComponent::fromLabel('Submit');
 *     $button->handle($event);  // ComponentState::Submitted on Enter or Space (when focused)
 *     $button->focus();         // show yellow style on build()
 *     $button->build();         // returns SubmitFieldWidget for rendering
 *     $button->isFocused();     // returns true after focus(), false after unfocus()
 */
final class SubmitFieldComponent implements Field
{
    private bool $focused = false;

    private function __construct(
        private readonly string $label,
    ) {
    }

    public static function fromLabel(string $label): self
    {
        return new self($label);
    }

    public function handle(Event $event): ComponentState
    {
        if (!$this->focused) {
            return ComponentState::Ignored;
        }

        if ($event instanceof CodedKeyEvent && KeyCode::Enter === $event->code) {
            return ComponentState::Submitted;
        }

        if ($event instanceof CharKeyEvent && ' ' === $event->char) {
            return ComponentState::Submitted;
        }

        return ComponentState::Ignored;
    }

    public function build(): Widget
    {
        $widget = SubmitFieldWidget::fromLabel($this->label);

        return $this->focused ? $widget->focused() : $widget;
    }

    public function focus(): void
    {
        $this->focused = true;
    }

    public function unfocus(): void
    {
        $this->focused = false;
    }

    public function isFocused(): bool
    {
        return $this->focused;
    }
}
