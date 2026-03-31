<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField;

use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ValueField;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Tui\Widget\Widget;

/**
 * Handles text editing events and builds an InputFieldWidget on frame redraw.
 *
 * Usage:
 *     $field = InputFieldComponent::fromLabel('Username');
 *     $field = InputFieldComponent::fromLabel('Levels')->withValue('1');
 *     $field->handle($event);  // inserts/deletes chars, moves cursor (Ignored when unfocused)
 *     $field->focus();         // show cursor and yellow border on build()
 *     $field->build();         // returns InputFieldWidget for rendering
 *     $field->getValue();      // returns current text value
 */
final readonly class InputFieldComponent implements ValueField
{
    private function __construct(
        private string $label,
        private LineEditorComponent $lineEditorComponent,
    ) {
    }

    public static function fromLabel(string $label): self
    {
        return new self($label, LineEditorComponent::empty());
    }

    public function withValue(string $value): self
    {
        return new self($this->label, LineEditorComponent::fromValue($value));
    }

    public function handle(Event $event): ComponentState
    {
        if (!$this->isFocused()) {
            return ComponentState::Ignored;
        }

        return $this->lineEditorComponent->handle($event);
    }

    public function build(): Widget
    {
        return InputFieldWidget::fromLabel($this->label)
            ->lineEditorWidget($this->lineEditorComponent->build());
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->lineEditorComponent->getValue();
    }

    public function focus(): void
    {
        $this->lineEditorComponent->focus();
    }

    public function unfocus(): void
    {
        $this->lineEditorComponent->unfocus();
    }

    public function isFocused(): bool
    {
        return $this->lineEditorComponent->isFocused();
    }
}
