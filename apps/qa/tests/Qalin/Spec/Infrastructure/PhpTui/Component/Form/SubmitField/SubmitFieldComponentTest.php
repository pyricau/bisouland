<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form\SubmitField;

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

#[CoversClass(SubmitFieldComponent::class)]
#[Small]
final class SubmitFieldComponentTest extends TestCase
{
    #[TestDox('It reports ComponentState::Submitted when focused and Enter is pressed')]
    public function test_it_reports_submitted_when_focused_and_enter_is_pressed(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $button->focus();

        $state = $button->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertSame(ComponentState::Submitted, $state);
    }

    #[TestDox('It reports ComponentState::Submitted when focused and Space is pressed')]
    public function test_it_reports_submitted_when_focused_and_space_is_pressed(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $button->focus();

        $state = $button->handle(CharKeyEvent::new(' '));

        $this->assertSame(ComponentState::Submitted, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when focused but the event is not Enter or Space (e.g. Tab)')]
    public function test_it_reports_ignored_when_focused_but_event_is_not_enter_or_space(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $button->focus();

        $state = $button->handle(CodedKeyEvent::new(KeyCode::Tab));

        $this->assertSame(ComponentState::Ignored, $state);
    }

    #[TestDox('It reports ComponentState::Ignored when unfocused')]
    public function test_it_reports_ignored_when_unfocused(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');

        $state = $button->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertSame(ComponentState::Ignored, $state);
    }

    #[TestDox('It builds SubmitFieldWidget snapshotting label and unfocused state by default')]
    public function test_it_builds_a_widget_with_the_label_and_unfocused_by_default(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');

        $widget = $button->build();

        $this->assertInstanceOf(SubmitFieldWidget::class, $widget);
        $this->assertSame('Submit', $widget->label);
        $this->assertFalse($widget->focused);
    }

    #[TestDox('It builds SubmitFieldWidget snapshotting focused state after focus()')]
    public function test_it_builds_a_focused_widget_after_focus(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $button->focus();

        $widget = $button->build();

        $this->assertInstanceOf(SubmitFieldWidget::class, $widget);
        $this->assertTrue($widget->focused);
    }

    #[TestDox('It builds SubmitFieldWidget snapshotting focused state after unfocus()')]
    public function test_it_builds_an_unfocused_widget_after_unfocus(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $button->focus();
        $button->unfocus();

        $widget = $button->build();

        $this->assertInstanceOf(SubmitFieldWidget::class, $widget);
        $this->assertFalse($widget->focused);
    }

    #[TestDox('It has isFocused()')]
    public function test_it_reports_is_focused_accurately(): void
    {
        $button = SubmitFieldComponent::fromLabel('Submit');
        $this->assertFalse($button->isFocused());

        $button->focus();
        $this->assertTrue($button->isFocused());

        $button->unfocus();
        $this->assertFalse($button->isFocused());
    }
}
