<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\InputField;

use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputFieldComponent::class)]
#[Small]
final class InputFieldComponentTest extends TestCase
{
    #[TestDox('It reports ComponentState::Changed when focused and the editor updates')]
    public function test_it_reports_changed_when_focused_and_editor_updates(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();

        $state = $field->handle(CharKeyEvent::new('b'));

        $this->assertSame(ComponentState::Changed, $state);
    }

    #[TestDox('It reports ComponentState::Handled when focused but the editor has nothing to do (e.g. Backspace at start)')]
    public function test_it_reports_handled_when_focused_but_editor_has_nothing_to_do(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Backspace));

        $this->assertSame(ComponentState::Handled, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when focused but the event is not recognized (e.g. Tab)')]
    public function test_it_reports_ignored_when_focused_but_event_is_not_recognized(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();

        $state = $field->handle(CodedKeyEvent::new(KeyCode::Tab));

        $this->assertSame(ComponentState::Ignored, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when unfocused')]
    public function test_it_reports_ignored_when_unfocused(): void
    {
        $field = InputFieldComponent::fromLabel('Username');

        $state = $field->handle(CharKeyEvent::new('b'));

        $this->assertSame(ComponentState::Ignored, $state);
        $this->assertSame('', $field->getValue()); // no input was recorded
    }

    #[TestDox('It builds InputFieldWidget snapshotting label and LineEditor state (value, cursor position)')]
    public function test_it_builds_a_widget_with_the_label_and_current_editor_state(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();
        $field->handle(CharKeyEvent::new('b'));
        $field->handle(CharKeyEvent::new('l'));
        $field->handle(CodedKeyEvent::new(KeyCode::Left));

        $widget = $field->build();

        $this->assertInstanceOf(InputFieldWidget::class, $widget);
        $this->assertSame('Username', $widget->label);
        $this->assertSame('bl', $widget->lineEditorWidget->value);
        $this->assertSame(1, $widget->lineEditorWidget->cursorPosition);
    }

    #[TestDox('It builds InputFieldWidget snapshotting focused state after focus()')]
    public function test_it_builds_a_focused_widget_after_focus(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();

        $widget = $field->build();

        $this->assertInstanceOf(InputFieldWidget::class, $widget);
        $this->assertTrue($widget->lineEditorWidget->focused);
    }

    #[TestDox('It builds InputFieldWidget snapshotting focused state after unfocus()')]
    public function test_it_builds_an_unfocused_widget_after_unfocus(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();
        $field->unfocus();

        $widget = $field->build();

        $this->assertInstanceOf(InputFieldWidget::class, $widget);
        $this->assertFalse($widget->lineEditorWidget->focused);
    }

    #[TestDox('It has label')]
    public function test_it_exposes_the_label(): void
    {
        $field = InputFieldComponent::fromLabel('Username');

        $this->assertSame('Username', $field->getLabel());
    }

    #[TestDox('It proxies isFocused() to LineEditor')]
    public function test_it_reports_is_focused_accurately(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $this->assertFalse($field->isFocused());

        $field->focus();
        $this->assertTrue($field->isFocused());

        $field->unfocus();
        $this->assertFalse($field->isFocused());
    }

    #[TestDox('It proxies getValue() to LineEditor')]
    public function test_it_exposes_the_current_value(): void
    {
        $field = InputFieldComponent::fromLabel('Username');
        $field->focus();
        $field->handle(CharKeyEvent::new('b'));
        $field->handle(CharKeyEvent::new('l'));

        $this->assertSame('bl', $field->getValue());
    }

    #[TestDox('It has withValue() setting an initial value')]
    public function test_it_has_with_value_setting_an_initial_value(): void
    {
        $field = InputFieldComponent::fromLabel('Levels')->withValue('1');

        $this->assertSame('1', $field->getValue());
    }
}
