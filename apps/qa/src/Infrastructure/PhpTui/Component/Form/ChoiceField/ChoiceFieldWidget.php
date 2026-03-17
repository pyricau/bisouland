<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a labeled choice field with a filter input and a filterable list.
 *
 * Usage:
 *     $widget = ChoiceFieldWidget::fromLabel('Language')
 *         ->choices(['PHP', 'Python', 'JavaScript'])
 *         ->selectedIndex(1)
 *         ->focused()         // show REVERSED cursor and yellow border on render
 *     ;
 *
 *     // There are default styles, but can be customized:
 *     $widget = $widget
 *         ->focusedBorderStyle($focusedStyle)
 *         ->unfocusedBorderStyle($style)
 *         ->highlightStyle($style);
 */
final readonly class ChoiceFieldWidget implements Widget
{
    public bool $focused;

    /**
     * @param list<string> $choices
     */
    private function __construct(
        public string $label,
        public LineEditorWidget $lineEditorWidget,
        public array $choices,
        public int $selectedIndex,
        public Style $focusedBorderStyle,
        public Style $unfocusedBorderStyle,
        public Style $highlightStyle,
    ) {
        $this->focused = $lineEditorWidget->focused;
    }

    public static function fromLabel(string $label): self
    {
        if ('' === $label) {
            throw ValidationFailedException::make(
                'Invalid "ChoiceFieldWidget" parameter: label should not be empty (`""` given)',
            );
        }

        return new self(
            $label,
            LineEditorWidget::empty(),
            [],
            0,
            Style::default()->fg(AnsiColor::Yellow),
            Style::default(),
            Style::default()->addModifier(Modifier::REVERSED),
        );
    }

    public function lineEditorWidget(LineEditorWidget $lineEditorWidget): self
    {
        return new self(
            $this->label,
            $lineEditorWidget,
            $this->choices,
            $this->selectedIndex,
            $this->focusedBorderStyle,
            $this->unfocusedBorderStyle,
            $this->highlightStyle,
        );
    }

    /** @param list<string> $choices */
    public function choices(array $choices): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $choices,
            $this->selectedIndex,
            $this->focusedBorderStyle,
            $this->unfocusedBorderStyle,
            $this->highlightStyle,
        );
    }

    public function selectedIndex(int $selectedIndex): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $this->choices,
            $selectedIndex,
            $this->focusedBorderStyle,
            $this->unfocusedBorderStyle,
            $this->highlightStyle,
        );
    }

    // Convenience withers that proxy to the lineEditorWidget:

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
            $this->choices,
            $this->selectedIndex,
            $focusedBorderStyle,
            $this->unfocusedBorderStyle,
            $this->highlightStyle,
        );
    }

    public function unfocusedBorderStyle(Style $unfocusedBorderStyle): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $this->choices,
            $this->selectedIndex,
            $this->focusedBorderStyle,
            $unfocusedBorderStyle,
            $this->highlightStyle,
        );
    }

    public function highlightStyle(Style $highlightStyle): self
    {
        return new self(
            $this->label,
            $this->lineEditorWidget,
            $this->choices,
            $this->selectedIndex,
            $this->focusedBorderStyle,
            $this->unfocusedBorderStyle,
            $highlightStyle,
        );
    }
}
