<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField;

use Bl\Exception\ValidationFailedException;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a focusable submit button.
 *
 * Usage:
 *     $widget = SubmitFieldWidget::fromLabel('Submit')->focused();   // show yellow style on render
 *     $widget = SubmitFieldWidget::fromLabel('Submit')->unfocused(); // plain style (default)
 *
 *     // There are default styles, but can be customized:
 *     $widget = $widget
 *         ->focusedStyle($focusedStyle)
 *         ->unfocusedStyle($style);
 */
final readonly class SubmitFieldWidget implements Widget
{
    private function __construct(
        public string $label,
        public bool $focused,
        public Style $focusedStyle,
        public Style $unfocusedStyle,
    ) {
    }

    public static function fromLabel(string $label): self
    {
        if ('' === $label) {
            throw ValidationFailedException::make(
                'Invalid "SubmitFieldWidget" parameter: label should not be empty (`""` given)',
            );
        }

        return new self(
            $label,
            false,
            Style::default()->fg(AnsiColor::Yellow),
            Style::default(),
        );
    }

    public function focused(): self
    {
        return new self($this->label, true, $this->focusedStyle, $this->unfocusedStyle);
    }

    public function unfocused(): self
    {
        return new self($this->label, false, $this->focusedStyle, $this->unfocusedStyle);
    }

    public function focusedStyle(Style $focusedStyle): self
    {
        return new self($this->label, $this->focused, $focusedStyle, $this->unfocusedStyle);
    }

    public function unfocusedStyle(Style $unfocusedStyle): self
    {
        return new self($this->label, $this->focused, $this->focusedStyle, $unfocusedStyle);
    }
}
