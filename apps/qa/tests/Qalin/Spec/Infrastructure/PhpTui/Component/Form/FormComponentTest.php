<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormComponent::class)]
#[Small]
final class FormComponentTest extends TestCase
{
    #[TestDox('It reports ComponentState::Changed when pressing Tab (cycles focus to next field)')]
    public function test_it_reports_changed_when_pressing_tab(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);

        $state = $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → password

        $this->assertSame(ComponentState::Changed, $state);
        $this->assertTrue($password->isFocused());
    }

    #[TestDox('It reports ComponentState::Changed when pressing Tab on the last field (wraps to first)')]
    public function test_it_reports_changed_when_pressing_tab_on_last_field(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);
        $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → password
        $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // password → submit

        $state = $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // submit → username (wrap)

        $this->assertSame(ComponentState::Changed, $state);
        $this->assertTrue($username->isFocused());
    }

    #[TestDox('It reports ComponentState::Changed when pressing BackTab (cycles focus to previous field)')]
    public function test_it_reports_changed_when_pressing_backtab(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);
        $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → password

        $state = $form->handle(CodedKeyEvent::new(KeyCode::BackTab)); // password → username

        $this->assertSame(ComponentState::Changed, $state);
        $this->assertTrue($username->isFocused());
    }

    #[TestDox('It reports ComponentState::Changed when pressing BackTab on the first field (wraps to last)')]
    public function test_it_reports_changed_when_pressing_backtab_on_first_field(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);

        $state = $form->handle(CodedKeyEvent::new(KeyCode::BackTab)); // username → submit (wrap)

        $this->assertSame(ComponentState::Changed, $state);
        $this->assertTrue($submit->isFocused());
    }

    #[TestDox('It reports ComponentState::Changed when the focused field handles the event (e.g. char key on InputField)')]
    public function test_it_reports_changed_when_focused_input_handles_event(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $submit);

        $state = $form->handle(CharKeyEvent::new('b'));

        $this->assertSame(ComponentState::Changed, $state);
        $this->assertSame('b', $username->getValue());
    }

    #[TestDox('It reports ComponentState::Ignored when the focused field ignores the event (e.g. Enter on InputField)')]
    public function test_it_reports_ignored_when_focused_input_ignores_event(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $submit);

        $state = $form->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertSame(ComponentState::Ignored, $state);
    }

    #[TestDox('It reports ComponentState::Submitted when the focused SubmitField handles the event (e.g. Enter)')]
    public function test_it_reports_submitted_when_focused_submit_handles_event(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $submit);

        $form->handle(CodedKeyEvent::new(KeyCode::Tab));
        // focus submit
        $state = $form->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertSame(ComponentState::Submitted, $state);
    }

    #[TestDox('It reports ComponentState::Submitted when Enter is pressed and no SubmitField is present')]
    public function test_it_reports_submitted_when_enter_and_no_submit_field(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $form = FormComponent::fromFields($username);

        $state = $form->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertSame(ComponentState::Submitted, $state);
    }

    #[TestDox('It builds FormWidget snapshotting all fields in order')]
    public function test_it_builds_a_form_widget_with_all_fields(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);

        $widget = $form->build();

        $this->assertInstanceOf(FormWidget::class, $widget);
        $this->assertInstanceOf(InputFieldWidget::class, $widget->items[0]);
        $this->assertInstanceOf(InputFieldWidget::class, $widget->items[1]);
        $this->assertInstanceOf(SubmitFieldWidget::class, $widget->items[2]);
    }

    #[TestDox('It has the first field focused on construction')]
    public function test_it_focuses_first_field_on_construction(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');

        FormComponent::fromFields($username, $password, $submit);

        $this->assertTrue($username->isFocused());
        $this->assertFalse($password->isFocused());
        $this->assertFalse($submit->isFocused());
    }

    #[TestDox('It has isSubmitted()')]
    public function test_it_reports_is_submitted_accurately(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $submit);

        $this->assertFalse($form->isSubmitted()); // false initially

        $form->handle(CharKeyEvent::new('b')); // input change does not submit
        $this->assertFalse($form->isSubmitted());

        $form->handle(CodedKeyEvent::new(KeyCode::Tab));   // focus submit
        $form->handle(CodedKeyEvent::new(KeyCode::Enter)); // press submit
        $this->assertTrue($form->isSubmitted());
    }

    #[TestDox('It has getValues() (only ValueField instances)')]
    public function test_it_exposes_get_values_for_form_submission(): void
    {
        $username = InputFieldComponent::fromLabel('Username');
        $password = InputFieldComponent::fromLabel('Password');
        $submit = SubmitFieldComponent::fromLabel('Submit');
        $form = FormComponent::fromFields($username, $password, $submit);

        $form->handle(CharKeyEvent::new('b'));
        $form->handle(CharKeyEvent::new('l'));
        $form->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → password
        $form->handle(CharKeyEvent::new('x'));

        $this->assertSame(['Username' => 'bl', 'Password' => 'x'], $form->getValues());
    }

    #[TestDox('It fails when fields is empty (`[]` given)')]
    public function test_it_fails_when_fields_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        FormComponent::fromFields();
    }
}
