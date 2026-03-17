<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a labeled text input field.
 *
 * Usage:
 *     $widget = InputFieldWidget::fromLabel('Username')
 *         // Convenience withers that proxy to the lineEditorWidget:
 *         ->value('blackadder')   // moves cursor to end
 *         ->cursorPosition(0)     // override cursor position
 *         ->focused()             // show REVERSED cursor on render
 *         ->unfocused()           // hide cursor
 *     ;
 *
 *     // Or pass a LineEditorWidget directly:
 *     $widget = InputFieldWidget::fromLabel('Username')
 *         ->lineEditorWidget($lineEditorWidget);
 *
 *     // There are default styles, but can be customized:
 *     $widget = $widget
 *         ->focusedBorderStyle($focusedStyle)
 *         ->unfocusedBorderStyle($style);
 */
final readonly class InputFieldWidget implements Widget
{
    public bool $focused;

    private function __construct(
        public string $label,
        public LineEditorWidget $lineEditorWidget,
        public Style $focusedBorderStyle,
        public Style $unfocusedBorderStyle,
    ) {
        $this->focused = $lineEditorWidget->focused;
    }

    public static function fromLabel(string $label): self
    {
        if ('' === $label) {
            throw ValidationFailedException::make(
                'Invalid "InputFieldWidget" parameter: label should not be empty (`""` given)',
            );
        }

        return new self(
            $label,
            LineEditorWidget::empty(),
            Style::default()->fg(AnsiColor::Yellow),
            Style::default(),
        );
    }

    public function lineEditorWidget(LineEditorWidget $lineEditorWidget): self
    {
        return new self(
            $this->label,
            $lineEditorWidget,
            $this->focusedBorderStyle,
            $this->unfocusedBorderStyle,
        );
    }

    // Convenience withers that proxy to the lineEditorWidget:

    public function value(string $value): self
    {
        return $this->lineEditorWidget($this->lineEditorWidget->value($value));
    }

    public function cursorPosition(int $cursorPosition): self
    {
        return $this->lineEditorWidget($this->lineEditorWidget->cursorPosition($cursorPosition));
    }

    public function focused(): self
    {
        return $this->lineEditorWidget($this->lineEditorWidget->focused());
    }

    public function unfocused(): self
    {
        return $this->lineEditorWidget($this->lineEditorWidget->unfocused());
    }

    public function focusedBorderStyle(Style $focusedBorderStyle): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $focusedBorderStyle,
            $this->unfocusedBorderStyle,
        );
    }

    public function unfocusedBorderStyle(Style $unfocusedBorderStyle): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $this->focusedBorderStyle,
            $unfocusedBorderStyle,
        );
    }
}
