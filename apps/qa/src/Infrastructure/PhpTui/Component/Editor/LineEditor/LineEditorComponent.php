<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor;

use Bl\Qa\Infrastructure\PhpTui\Component;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;

/**
 * Maintains a text value with a cursor position, and handles keyboard events.
 *
 * Supports standard editing keys (arrows, Home, End, Backspace, Delete)
 * and Emacs bindings:
 *     Ctrl+A / Ctrl+E    move to start / end
 *     Ctrl+B / Ctrl+F    move back / forward one char
 *     Ctrl+D             delete char at cursor
 *     Ctrl+K / Ctrl+U    kill to end / start of line
 *     Ctrl+W             kill word backward
 *     Alt+B  / Alt+F     move back / forward one word
 *     Alt+D              kill word forward
 *
 * Usage:
 *     $editor = LineEditorComponent::empty();
 *     $editor = LineEditorComponent::fromValue('baldrick');  // cursor at end
 *     $editor->handle($event);  // inserts/deletes chars, moves cursor
 *     $editor->focus();         // build() will render with REVERSED cursor
 *     $editor->build();         // returns LineEditorWidget for rendering
 *     $editor->getValue();      // returns current text value
 */
final class LineEditorComponent implements Component
{
    /** @var int<0, max> */
    private int $cursorPosition;

    private bool $focused = false;

    private function __construct(
        private string $value,
    ) {
        $this->cursorPosition = mb_strlen($value);
    }

    public static function empty(): self
    {
        return new self('');
    }

    public static function fromValue(string $value): self
    {
        return new self($value);
    }

    public function handle(Event $event): ComponentState
    {
        if ($event instanceof CharKeyEvent) {
            if (KeyModifiers::CONTROL === $event->modifiers) {
                return match ($event->char) {
                    'a' => $this->moveCursorToStart(),
                    'b' => $this->moveCursorLeft(),
                    'd' => $this->deleteCharAt(),
                    'e' => $this->moveCursorToEnd(),
                    'f' => $this->moveCursorRight(),
                    'k' => $this->killToEnd(),
                    'u' => $this->killToStart(),
                    'w' => $this->killWordBackward(),
                    default => ComponentState::Ignored,
                };
            }

            if (KeyModifiers::ALT === $event->modifiers) {
                return match ($event->char) {
                    'b' => $this->moveWordBackward(),
                    'd' => $this->killWordForward(),
                    'f' => $this->moveWordForward(),
                    default => ComponentState::Ignored,
                };
            }

            if (
                KeyModifiers::NONE !== $event->modifiers
                && KeyModifiers::SHIFT !== $event->modifiers
            ) {
                return ComponentState::Ignored;
            }

            $before = mb_substr($this->value, 0, $this->cursorPosition);
            $after = mb_substr($this->value, $this->cursorPosition);
            $this->value = "{$before}{$event->char}{$after}";
            ++$this->cursorPosition;

            return ComponentState::Changed;
        }

        if (!$event instanceof CodedKeyEvent) {
            return ComponentState::Ignored;
        }

        return match ($event->code) {
            KeyCode::Backspace => $this->deleteCharBefore(),
            KeyCode::Delete => $this->deleteCharAt(),
            KeyCode::Left => $this->moveCursorLeft(),
            KeyCode::Right => $this->moveCursorRight(),
            KeyCode::Home => $this->moveCursorToStart(),
            KeyCode::End => $this->moveCursorToEnd(),
            default => ComponentState::Ignored,
        };
    }

    public function build(): LineEditorWidget
    {
        $widget = LineEditorWidget::empty()
            ->value($this->value)
            ->cursorPosition($this->cursorPosition);

        return $this->focused ? $widget->focused() : $widget;
    }

    public function getValue(): string
    {
        return $this->value;
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

    private function deleteCharBefore(): ComponentState
    {
        if (0 === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $before = mb_substr($this->value, 0, $this->cursorPosition - 1);
        $after = mb_substr($this->value, $this->cursorPosition);
        $this->value = "{$before}{$after}";
        --$this->cursorPosition;

        return ComponentState::Changed;
    }

    private function deleteCharAt(): ComponentState
    {
        if ($this->cursorPosition >= mb_strlen($this->value)) {
            return ComponentState::Handled;
        }

        $before = mb_substr($this->value, 0, $this->cursorPosition);
        $after = mb_substr($this->value, $this->cursorPosition + 1);
        $this->value = "{$before}{$after}";

        return ComponentState::Changed;
    }

    private function moveCursorLeft(): ComponentState
    {
        if (0 === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        --$this->cursorPosition;

        return ComponentState::Changed;
    }

    private function moveCursorRight(): ComponentState
    {
        if ($this->cursorPosition >= mb_strlen($this->value)) {
            return ComponentState::Handled;
        }

        ++$this->cursorPosition;

        return ComponentState::Changed;
    }

    private function moveCursorToStart(): ComponentState
    {
        if (0 === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $this->cursorPosition = 0;

        return ComponentState::Changed;
    }

    private function moveCursorToEnd(): ComponentState
    {
        $end = mb_strlen($this->value);
        if ($this->cursorPosition === $end) {
            return ComponentState::Handled;
        }

        $this->cursorPosition = $end;

        return ComponentState::Changed;
    }

    private function killToEnd(): ComponentState
    {
        if ($this->cursorPosition >= mb_strlen($this->value)) {
            return ComponentState::Handled;
        }

        $this->value = mb_substr($this->value, 0, $this->cursorPosition);

        return ComponentState::Changed;
    }

    private function killToStart(): ComponentState
    {
        if (0 === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $this->value = mb_substr($this->value, $this->cursorPosition);
        $this->cursorPosition = 0;

        return ComponentState::Changed;
    }

    private function moveWordForward(): ComponentState
    {
        $pos = $this->findEndOfNextWord();
        if ($pos === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $this->cursorPosition = $pos;

        return ComponentState::Changed;
    }

    private function moveWordBackward(): ComponentState
    {
        $pos = $this->findStartOfPrevWord();
        if ($pos === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $this->cursorPosition = $pos;

        return ComponentState::Changed;
    }

    private function killWordForward(): ComponentState
    {
        $pos = $this->findEndOfNextWord();
        if ($pos === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $before = mb_substr($this->value, 0, $this->cursorPosition);
        $after = mb_substr($this->value, $pos);
        $this->value = "{$before}{$after}";

        return ComponentState::Changed;
    }

    private function killWordBackward(): ComponentState
    {
        $pos = $this->findStartOfPrevWord();
        if ($pos === $this->cursorPosition) {
            return ComponentState::Handled;
        }

        $before = mb_substr($this->value, 0, $pos);
        $after = mb_substr($this->value, $this->cursorPosition);
        $this->value = "{$before}{$after}";
        $this->cursorPosition = $pos;

        return ComponentState::Changed;
    }

    /** @return int<0, max> */
    private function findEndOfNextWord(): int
    {
        $len = mb_strlen($this->value);
        $pos = $this->cursorPosition;
        while ($pos < $len && !preg_match('/\w/', mb_substr($this->value, $pos, 1))) {
            ++$pos;
        }

        while ($pos < $len && preg_match('/\w/', mb_substr($this->value, $pos, 1))) {
            ++$pos;
        }

        return $pos;
    }

    /** @return int<0, max> */
    private function findStartOfPrevWord(): int
    {
        $pos = $this->cursorPosition;
        while ($pos > 0 && !preg_match('/\w/', mb_substr($this->value, $pos - 1, 1))) {
            --$pos;
        }

        while ($pos > 0 && preg_match('/\w/', mb_substr($this->value, $pos - 1, 1))) {
            --$pos;
        }

        return $pos;
    }
}
