<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\ChoiceField;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChoiceFieldComponent::class)]
#[Small]
final class ChoiceFieldComponentTest extends TestCase
{
    #[TestDox('It reports ComponentState::Changed when focused and the filter updates')]
    public function test_it_reports_changed_when_focused_and_filter_updates(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();

        $state = $field->handle(CharKeyEvent::new('z'));

        $this->assertSame(ComponentState::Changed, $state);
    }

    #[TestDox('It reports ComponentState::Changed when focused and navigating down (when more choices below)')]
    public function test_it_reports_changed_when_focused_and_navigating_down(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Down));

        $this->assertSame(ComponentState::Changed, $state);
    }

    #[TestDox('It reports ComponentState::Changed when focused and navigating up (when more choices above)')]
    public function test_it_reports_changed_when_focused_and_navigating_up(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();
        $field->handle(CodedKeyEvent::new(KeyCode::Down)); // move to index 1

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Up));

        $this->assertSame(ComponentState::Changed, $state);
    }

    #[TestDox('It reports ComponentState::Handled when focused and navigating down at last choice')]
    public function test_it_reports_handled_when_focused_and_navigating_down_at_last_choice(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();
        $field->handle(CodedKeyEvent::new(KeyCode::Down)); // move to last (index 1)

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Down));

        $this->assertSame(ComponentState::Handled, $state);
    }

    #[TestDox('It reports ComponentState::Handled when focused and navigating up at first choice')]
    public function test_it_reports_handled_when_focused_and_navigating_up_at_first_choice(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Up));

        $this->assertSame(ComponentState::Handled, $state);
    }

    #[TestDox('It reports ComponentState::Handled when focused but the editor has nothing to do (e.g. Backspace at start)')]
    public function test_it_reports_handled_when_focused_but_editor_has_nothing_to_do(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Backspace));

        $this->assertSame(ComponentState::Handled, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when focused but the event is not recognized (e.g. Tab)')]
    public function test_it_reports_ignored_when_focused_but_event_is_not_recognized(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Tab));

        $this->assertSame(ComponentState::Ignored, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when unfocused')]
    public function test_it_reports_ignored_when_unfocused(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);

        $state = $field->handle(CharKeyEvent::new('z'));

        $this->assertSame(ComponentState::Ignored, $state);
        $this->assertSame('Medieval', $field->getValue()); // no filter was applied
    }

    #[TestDox('It builds ChoiceFieldWidget snapshotting label, all choices, and selectedIndex')]
    public function test_it_builds_a_widget_with_the_label_and_all_choices(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();
        $field->handle(CodedKeyEvent::new(KeyCode::Down));

        $widget = $field->build();

        $this->assertInstanceOf(ChoiceFieldWidget::class, $widget);
        $this->assertSame('Era', $widget->label);
        $this->assertSame(['Medieval', 'Elizabethan', 'Regency', 'World War I'], $widget->choices);
        $this->assertSame(1, $widget->selectedIndex);
    }

    #[TestDox('It builds ChoiceFieldWidget snapshotting focused state after focus()')]
    public function test_it_builds_a_focused_widget_after_focus(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();

        $widget = $field->build();

        $this->assertInstanceOf(ChoiceFieldWidget::class, $widget);
        $this->assertTrue($widget->lineEditorWidget->focused);
    }

    #[TestDox('It builds ChoiceFieldWidget snapshotting focused state after unfocus()')]
    public function test_it_builds_an_unfocused_widget_after_unfocus(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan']);
        $field->focus();
        $field->unfocus();

        $widget = $field->build();

        $this->assertInstanceOf(ChoiceFieldWidget::class, $widget);
        $this->assertFalse($widget->lineEditorWidget->focused);
    }

    #[TestDox('It builds ChoiceFieldWidget with filtered choices when filter matches')]
    public function test_it_builds_a_widget_with_filtered_choices(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();
        $field->handle(CharKeyEvent::new('z')); // 'z' matches only 'Elizabethan'

        $widget = $field->build();

        $this->assertInstanceOf(ChoiceFieldWidget::class, $widget);
        $this->assertSame(['Elizabethan'], $widget->choices);
    }

    #[TestDox('It builds ChoiceFieldWidget resetting selectedIndex when filter changes')]
    public function test_it_builds_a_widget_resetting_selected_index_on_filter_change(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();
        $field->handle(CodedKeyEvent::new(KeyCode::Down)); // selectedIndex = 1
        $field->handle(CharKeyEvent::new('z'));            // filter changes → reset to 0

        $widget = $field->build();

        $this->assertInstanceOf(ChoiceFieldWidget::class, $widget);
        $this->assertSame(0, $widget->selectedIndex);
    }

    #[TestDox('It has label')]
    public function test_it_exposes_the_label(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval']);

        $this->assertSame('Era', $field->getLabel());
    }

    #[TestDox('It proxies isFocused() to LineEditor')]
    public function test_it_reports_is_focused_accurately(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval']);
        $this->assertFalse($field->isFocused());

        $field->focus();
        $this->assertTrue($field->isFocused());

        $field->unfocus();
        $this->assertFalse($field->isFocused());
    }

    #[TestDox('It has getValue() returning the currently selected (filtered) choice')]
    public function test_it_exposes_the_selected_choice_as_value(): void
    {
        $field = ChoiceFieldComponent::fromLabelAndChoices('Era', ['Medieval', 'Elizabethan', 'Regency', 'World War I']);
        $field->focus();
        $field->handle(CodedKeyEvent::new(KeyCode::Down));

        $this->assertSame('Elizabethan', $field->getValue());
    }

    #[TestDox('It fails when choices is empty (`[]` given)')]
    public function test_it_fails_when_choices_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        ChoiceFieldComponent::fromLabelAndChoices('Era', []);
    }
}
