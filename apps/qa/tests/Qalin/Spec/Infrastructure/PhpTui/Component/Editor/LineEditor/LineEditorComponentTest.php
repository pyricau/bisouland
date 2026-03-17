<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Editor\LineEditor;

use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\Event\CursorPositionEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LineEditorComponent::class)]
#[Small]
final class LineEditorComponentTest extends TestCase
{
    /** @param list<Event> $setup */
    #[DataProvider('changedEventsProvider')]
    #[TestDox('It reports ComponentState::Changed when $scenario')]
    public function test_it_reports_changed(string $scenario, array $setup, Event $event): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        foreach ($setup as $setupEvent) {
            $lineEditorComponent->handle($setupEvent);
        }

        $state = $lineEditorComponent->handle($event);

        $this->assertSame(ComponentState::Changed, $state);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     setup: list<Event>,
     *     event: Event,
     * }>
     */
    public static function changedEventsProvider(): \Generator
    {
        $typeBaldrick = array_map(
            static fn (string $char): CharKeyEvent => CharKeyEvent::new($char),
            str_split('baldrick'),
        );
        $moveCursorToMiddle = array_fill(0, 5, CodedKeyEvent::new(KeyCode::Left));

        yield [
            'scenario' => 'typing a char',
            'setup' => [],
            'event' => CharKeyEvent::new('b'),
        ];
        yield [
            'scenario' => 'typing an uppercase char (Shift modifier)',
            'setup' => [],
            'event' => CharKeyEvent::new('A', KeyModifiers::SHIFT),
        ];
        yield [
            'scenario' => 'pressing Backspace when cursor is not at start',
            'setup' => [CharKeyEvent::new('b')],
            'event' => CodedKeyEvent::new(KeyCode::Backspace),
        ];
        yield [
            'scenario' => 'pressing Delete when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CodedKeyEvent::new(KeyCode::Delete),
        ];
        yield [
            'scenario' => 'pressing Left when cursor is not at start',
            'setup' => [CharKeyEvent::new('b')],
            'event' => CodedKeyEvent::new(KeyCode::Left),
        ];
        yield [
            'scenario' => 'pressing Right when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CodedKeyEvent::new(KeyCode::Right),
        ];
        yield [
            'scenario' => 'pressing Home when cursor is not at start',
            'setup' => [CharKeyEvent::new('b')],
            'event' => CodedKeyEvent::new(KeyCode::Home),
        ];
        yield [
            'scenario' => 'pressing End when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CodedKeyEvent::new(KeyCode::End),
        ];
        yield [
            'scenario' => 'pressing Ctrl+A when cursor is not at start',
            'setup' => [CharKeyEvent::new('b')],
            'event' => CharKeyEvent::new('a', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+B when cursor is not at start',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('b', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+D when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('d', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+E when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('e', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+F when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('f', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+K when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('k', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+U when cursor is not at start',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('u', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+W when cursor is not at start',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('w', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Alt+B when cursor is not at word start',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('b', KeyModifiers::ALT),
        ];
        yield [
            'scenario' => 'pressing Alt+D when cursor is not at end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('d', KeyModifiers::ALT),
        ];
        yield [
            'scenario' => 'pressing Alt+F when cursor is not at word end',
            'setup' => [...$typeBaldrick, ...$moveCursorToMiddle],
            'event' => CharKeyEvent::new('f', KeyModifiers::ALT),
        ];
    }

    /** @param list<Event> $setup */
    #[DataProvider('handledEventsProvider')]
    #[TestDox('It reports ComponentState::Handled when $scenario')]
    public function test_it_reports_handled(string $scenario, array $setup, Event $event): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        foreach ($setup as $setupEvent) {
            $lineEditorComponent->handle($setupEvent);
        }

        $state = $lineEditorComponent->handle($event);

        $this->assertSame(ComponentState::Handled, $state);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     setup: list<Event>,
     *     event: Event,
     * }>
     */
    public static function handledEventsProvider(): \Generator
    {
        yield [
            'scenario' => 'pressing Backspace when cursor is already at start',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::Backspace),
        ];
        yield [
            'scenario' => 'pressing Delete when cursor is already at end',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::Delete),
        ];
        yield [
            'scenario' => 'pressing Left when cursor is already at start',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::Left),
        ];
        yield [
            'scenario' => 'pressing Right when cursor is already at end',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::Right),
        ];
        yield [
            'scenario' => 'pressing Home when cursor is already at start',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::Home),
        ];
        yield [
            'scenario' => 'pressing End when cursor is already at end',
            'setup' => [],
            'event' => CodedKeyEvent::new(KeyCode::End),
        ];
        yield [
            'scenario' => 'pressing Ctrl+A when cursor is already at start',
            'setup' => [],
            'event' => CharKeyEvent::new('a', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+B when cursor is already at start',
            'setup' => [],
            'event' => CharKeyEvent::new('b', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+D when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('d', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+E when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('e', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+F when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('f', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+K when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('k', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+U when cursor is already at start',
            'setup' => [],
            'event' => CharKeyEvent::new('u', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Ctrl+W when cursor is already at start',
            'setup' => [],
            'event' => CharKeyEvent::new('w', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing Alt+B when cursor is already at start',
            'setup' => [],
            'event' => CharKeyEvent::new('b', KeyModifiers::ALT),
        ];
        yield [
            'scenario' => 'pressing Alt+D when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('d', KeyModifiers::ALT),
        ];
        yield [
            'scenario' => 'pressing Alt+F when cursor is already at end',
            'setup' => [],
            'event' => CharKeyEvent::new('f', KeyModifiers::ALT),
        ];
    }

    #[DataProvider('ignoredEventsProvider')]
    #[TestDox('It reports ComponentState::Ignored when $scenario')]
    public function test_it_reports_ignored(string $scenario, Event $event): void
    {
        $lineEditorComponent = LineEditorComponent::empty();

        $state = $lineEditorComponent->handle($event);

        $this->assertSame(ComponentState::Ignored, $state);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     event: Event,
     * }>
     */
    public static function ignoredEventsProvider(): \Generator
    {
        yield [
            'scenario' => 'pressing an unhandled key (e.g. Esc)',
            'event' => CodedKeyEvent::new(KeyCode::Esc),
        ];
        yield [
            'scenario' => 'pressing an unhandled Ctrl combination (e.g. Ctrl+X)',
            'event' => CharKeyEvent::new('x', KeyModifiers::CONTROL),
        ];
        yield [
            'scenario' => 'pressing an unhandled Alt combination (e.g. Alt+X)',
            'event' => CharKeyEvent::new('x', KeyModifiers::ALT),
        ];
        yield [
            'scenario' => 'receiving a non-key event (e.g. CursorPositionEvent)',
            'event' => new CursorPositionEvent(0, 0),
        ];
    }

    #[TestDox('It builds LineEditorWidget snapshotting typed value and cursor position')]
    public function test_it_builds_a_widget_with_current_state(): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        $lineEditorComponent->handle(CharKeyEvent::new('b'));
        $lineEditorComponent->handle(CharKeyEvent::new('l'));
        $lineEditorComponent->handle(CodedKeyEvent::new(KeyCode::Left));

        $widget = $lineEditorComponent->build();

        $this->assertInstanceOf(LineEditorWidget::class, $widget);
        $this->assertSame('bl', $widget->value);
        $this->assertSame(1, $widget->cursorPosition);
        $this->assertFalse($widget->focused);
    }

    #[TestDox('It builds LineEditorWidget snapshotting focused state after focus()')]
    public function test_it_builds_a_focused_widget_after_focus(): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        $lineEditorComponent->focus();

        $widget = $lineEditorComponent->build();

        $this->assertInstanceOf(LineEditorWidget::class, $widget);
        $this->assertTrue($widget->focused);
    }

    #[TestDox('It builds LineEditorWidget snapshotting focused state after unfocus()')]
    public function test_it_builds_an_unfocused_widget_after_unfocus(): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        $lineEditorComponent->focus();
        $lineEditorComponent->unfocus();

        $widget = $lineEditorComponent->build();

        $this->assertInstanceOf(LineEditorWidget::class, $widget);
        $this->assertFalse($widget->focused);
    }

    #[TestDox('It builds LineEditorWidget snapshotting initial value with cursor at end')]
    public function test_it_can_be_created_from_value(): void
    {
        $lineEditorComponent = LineEditorComponent::fromValue('baldrick');

        $widget = $lineEditorComponent->build();

        $this->assertSame('baldrick', $widget->value);
        $this->assertSame(8, $widget->cursorPosition);
    }

    #[TestDox('It has isFocused()')]
    public function test_it_reports_is_focused_accurately(): void
    {
        $lineEditorComponent = LineEditorComponent::empty();

        $this->assertFalse($lineEditorComponent->isFocused());

        $lineEditorComponent->focus();
        $this->assertTrue($lineEditorComponent->isFocused());

        $lineEditorComponent->unfocus();
        $this->assertFalse($lineEditorComponent->isFocused());
    }

    #[TestDox('It has getValue()')]
    public function test_it_exposes_the_current_value(): void
    {
        $lineEditorComponent = LineEditorComponent::empty();
        $lineEditorComponent->handle(CharKeyEvent::new('b'));
        $lineEditorComponent->handle(CharKeyEvent::new('l'));

        $this->assertSame('bl', $lineEditorComponent->getValue());
    }
}
