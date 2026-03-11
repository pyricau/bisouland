<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;

/**
 * Manages focus cycling and event delegation across form fields.
 *
 * Usage (with SubmitFieldComponent, Tab cycles through all fields including the button):
 *     $form = FormComponent::fromFields($username, $password, $submit);
 *     $form->handle($event);   // Tab/BackTab cycles focus; Enter/Space on SubmitField submits
 *     $form->isSubmitted();    // true after SubmitFieldComponent returns ComponentState::Submitted
 *     $form->build();          // returns FormWidget for rendering
 *     $form->getValues();      // ['Username' => '...', 'Password' => '...']
 *
 * Usage (without SubmitFieldComponent, Enter submits from any field):
 *     $form = FormComponent::fromFields($username, $password);
 *     $form->handle($event);   // Tab/BackTab cycles focus; Enter submits
 *     $form->isSubmitted();    // true after Enter is pressed on any field
 *     $form->build();          // returns FormWidget for rendering
 *     $form->getValues();      // ['Username' => '...', 'Password' => '...']
 */
final class FormComponent implements Component
{
    /** @var int<0, max> */
    private int $focusedIndex = 0;

    private bool $submitted = false;

    /**
     * @param non-empty-list<Field> $fields       ordered as passed to the factory
     * @param positive-int          $fieldCount   cached to avoid repeated count() calls in focus cycling
     * @param bool                  $enterSubmits auto-detected: true when all fields are ValueField (no SubmitField present)
     */
    private function __construct(
        private readonly array $fields,
        private readonly int $fieldCount,
        private readonly bool $enterSubmits,
    ) {
        $this->fields[0]->focus();
    }

    public static function fromFields(Field ...$fields): self
    {
        if ([] === $fields) {
            throw ValidationFailedException::make(
                'Invalid "FormComponent" parameter: fields should not be empty (`[]` given)',
            );
        }

        $fieldList = array_values($fields);
        $enterSubmits = array_all($fieldList, static fn (Field $field): bool => $field instanceof ValueField);

        return new self($fieldList, \count($fieldList), $enterSubmits);
    }

    public function handle(Event $event): ComponentState
    {
        if ($event instanceof CodedKeyEvent && KeyCode::Tab === $event->code) {
            return $this->focusNext();
        }

        if ($event instanceof CodedKeyEvent && KeyCode::BackTab === $event->code) {
            return $this->focusPrevious();
        }

        if ($this->enterSubmits
            && $event instanceof CodedKeyEvent
            && KeyCode::Enter === $event->code
        ) {
            $this->submitted = true;

            return ComponentState::Submitted;
        }

        $state = $this->focusedField()->handle($event);

        if (ComponentState::Submitted === $state) {
            $this->submitted = true;
        }

        return $state;
    }

    public function build(): Widget
    {
        return FormWidget::fromItems(
            ...array_map(static fn (Field $field): Widget => $field->build(), $this->fields),
        );
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    /**
     * @return array<string, string> label => value
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->fields as $field) {
            if ($field instanceof ValueField) {
                $values[$field->getLabel()] = $field->getValue();
            }
        }

        return $values;
    }

    private function focusNext(): ComponentState
    {
        $this->focusedField()->unfocus();
        $this->focusedIndex = ($this->focusedIndex + 1) % $this->fieldCount; // allows to wrap from last to first
        $this->focusedField()->focus();

        return ComponentState::Changed;
    }

    private function focusPrevious(): ComponentState
    {
        $this->focusedField()->unfocus();
        $this->focusedIndex = ($this->focusedIndex - 1 + $this->fieldCount) % $this->fieldCount; // allows to wrap from first to last
        $this->focusedField()->focus();

        return ComponentState::Changed;
    }

    private function focusedField(): Field
    {
        return $this->fields[$this->focusedIndex];
    }
}
