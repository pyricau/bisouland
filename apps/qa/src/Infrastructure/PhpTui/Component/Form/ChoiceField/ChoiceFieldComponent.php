<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ValueField;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;

/**
 * Handles choice selection and text filtering, builds a ChoiceFieldWidget on frame redraw.
 *
 * Usage:
 *     $field = ChoiceFieldComponent::fromLabelAndChoices('Language', ['PHP', 'Python', 'JavaScript']);
 *     $field->handle($event);  // Up/Down to navigate list, chars to filter (Ignored when unfocused)
 *     $field->focus();         // show cursor and yellow border on build()
 *     $field->build();         // returns ChoiceFieldWidget for rendering
 *     $field->getValue();      // returns the currently selected (filtered) choice
 */
final class ChoiceFieldComponent implements ValueField
{
    private int $selectedIndex = 0;

    /**
     * @param list<string> $choices
     */
    private function __construct(
        private readonly string $label,
        private readonly LineEditorComponent $lineEditorComponent,
        private readonly array $choices,
    ) {
    }

    /**
     * @param list<string> $choices
     */
    public static function fromLabelAndChoices(string $label, array $choices): self
    {
        if ([] === $choices) {
            throw ValidationFailedException::make(
                'Invalid "ChoiceFieldComponent" parameter: choices should not be empty (`[]` given)',
            );
        }

        return new self($label, LineEditorComponent::empty(), $choices);
    }

    public function handle(Event $event): ComponentState
    {
        if (!$this->isFocused()) {
            return ComponentState::Ignored;
        }

        if ($event instanceof CodedKeyEvent && KeyCode::Up === $event->code) {
            if ($this->selectedIndex > 0) {
                --$this->selectedIndex;

                return ComponentState::Changed;
            }

            return ComponentState::Handled;
        }

        if ($event instanceof CodedKeyEvent && KeyCode::Down === $event->code) {
            if ($this->selectedIndex < \count($this->filteredChoices()) - 1) {
                ++$this->selectedIndex;

                return ComponentState::Changed;
            }

            return ComponentState::Handled;
        }

        $previousFilter = $this->lineEditorComponent->getValue();
        $state = $this->lineEditorComponent->handle($event);
        if (ComponentState::Changed === $state && $previousFilter !== $this->lineEditorComponent->getValue()) {
            $this->selectedIndex = 0;
        }

        return $state;
    }

    public function build(): Widget
    {
        return ChoiceFieldWidget::fromLabel($this->label)
            ->lineEditorWidget($this->lineEditorComponent->build())
            ->choices($this->filteredChoices())
            ->selectedIndex($this->selectedIndex);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        $filtered = $this->filteredChoices();

        return $filtered[$this->selectedIndex] ?? '';
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

    /** @return list<string> */
    private function filteredChoices(): array
    {
        $filter = $this->lineEditorComponent->getValue();
        if ('' === $filter) {
            return $this->choices;
        }

        return array_values(array_filter(
            $this->choices,
            static fn (string $choice): bool => str_contains(strtolower($choice), strtolower($filter)),
        ));
    }
}
