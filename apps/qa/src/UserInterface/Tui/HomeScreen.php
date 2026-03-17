<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Action;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Quit;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use Bl\Qa\Infrastructure\PhpTui\Screen;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[Autoconfigure(public: true, shared: false)]
final class HomeScreen implements Screen
{
    /** @var list<Screen> */
    private readonly array $actions;

    /** @var list<Screen> */
    private readonly array $scenarios;

    /** @var HotkeyTabsComponent<HomeTab> */
    private HotkeyTabsComponent $tabs;

    private LineEditorComponent $fuzzyFind;

    /** @var list<Screen> */
    private array $filteredScreens;

    private int $cursorIndex = 0;

    /**
     * @param iterable<Screen> $actionScreens
     * @param iterable<Screen> $scenarioScreens
     */
    public function __construct(
        private readonly QalinAnimatedBanner $qalinAnimatedBanner,
        #[AutowireIterator('app.tui_action_screen')]
        iterable $actionScreens,
        #[AutowireIterator('app.tui_scenario_screen')]
        iterable $scenarioScreens,
    ) {
        $this->actions = iterator_to_array($actionScreens, false);
        $this->scenarios = iterator_to_array($scenarioScreens, false);
        $tabs = HomeTab::cases();
        /** @var HotkeyTabsComponent<HomeTab> $hotkeyTabs */
        $hotkeyTabs = HotkeyTabsComponent::fromTabs($tabs);
        $this->tabs = $hotkeyTabs;
        $this->fuzzyFind = LineEditorComponent::empty();
        $this->filteredScreens = $this->buildFilteredScreens();
    }

    public function name(): string
    {
        return 'Home';
    }

    public function build(): Widget
    {
        // Nav: Hotkey Tabs
        $nav = $this->tabs->build();

        // Content: FuzzyFind Action / Scenario screen
        $listItems = array_map(
            static fn (Screen $screen): ListItem => ListItem::fromString($screen->name()),
            $this->filteredScreens,
        );

        $list = ListWidget::default()
            ->highlightSymbol('> ')
            ->highlightStyle(Style::default()->fg(AnsiColor::Yellow)->addModifier(Modifier::BOLD));
        if ([] !== $listItems) {
            $list = $list->items(...$listItems)->select($this->cursorIndex);
        }

        $content = GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(3),
                Constraint::min(0),
            )
            ->widgets(
                // Fuzzy Find input
                BlockWidget::default()
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->borderStyle(Style::default()->fg(AnsiColor::Yellow))
                    ->titles(Title::fromString('Fuzzy Find'))
                    ->widget($this->fuzzyFind->build()),
                // List of Actions / Scenarios to select
                BlockWidget::default()
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->titles(Title::fromString($this->activeTab()->value))
                    ->widget($list),
            );

        return LayoutWidget::from(
            $this->qalinAnimatedBanner->widget(),
            $nav,
            $content,
            KeyHintsWidget::from(['Next' => 'Tab', 'Select' => 'Enter', 'Quit' => 'Esc']),
        );
    }

    public function handle(Event $event): Action
    {
        return match (true) {
            $event instanceof CodedKeyEvent => match ($event->code) {
                // Navigation
                KeyCode::Esc => new Quit(),
                // Fuzzy Find Field
                KeyCode::Backspace, KeyCode::Delete => $this->fuzzyFindDelete($event),
                // Selection
                KeyCode::Tab, KeyCode::Down => $this->cursorNext(),
                KeyCode::BackTab, KeyCode::Up => $this->cursorPrevious(),
                KeyCode::Enter => $this->selectCurrentScreen(),
                default => new Stay(),
            },
            $event instanceof CharKeyEvent => match ($this->fuzzyFind->getValue()) {
                // Tab Hotkeys (only if Fuzzy Find Field is empty).
                '' => match ($this->tabs->handle($event)) {
                    ComponentState::Changed => $this->resetFuzzyFind(),
                    ComponentState::Handled => new Stay(),
                    ComponentState::Ignored, ComponentState::Submitted => $this->fuzzyFindChar($event),
                },
                // Fuzzy Find Field
                default => $this->fuzzyFindChar($event),
            },
            default => new Stay(),
        };
    }

    private function activeTab(): HomeTab
    {
        return $this->tabs->isFocused();
    }

    /** @return list<Screen> */
    private function buildFilteredScreens(): array
    {
        $items = match ($this->activeTab()) {
            HomeTab::Actions => $this->actions,
            HomeTab::Scenarios => $this->scenarios,
        };
        $fuzzyFindValue = strtolower($this->fuzzyFind->getValue());
        if ('' === $fuzzyFindValue) {
            return $items;
        }

        return array_values(array_filter(
            $items,
            static fn (Screen $screen): bool => str_contains(strtolower($screen->name()), $fuzzyFindValue),
        ));
    }

    private function fuzzyFindDelete(CodedKeyEvent $event): Stay
    {
        $this->fuzzyFind->handle($event);
        $this->filteredScreens = $this->buildFilteredScreens();
        $this->cursorIndex = 0;

        return new Stay();
    }

    private function fuzzyFindChar(CharKeyEvent $event): Stay
    {
        $this->fuzzyFind->handle($event);
        $this->filteredScreens = $this->buildFilteredScreens();
        $this->cursorIndex = 0;

        return new Stay();
    }

    private function resetFuzzyFind(): Stay
    {
        $this->fuzzyFind = LineEditorComponent::empty();
        $this->filteredScreens = $this->buildFilteredScreens();
        $this->cursorIndex = 0;

        return new Stay();
    }

    private function selectCurrentScreen(): Action
    {
        $selected = $this->filteredScreens[$this->cursorIndex] ?? null;

        if (null === $selected) {
            return new Stay();
        }

        $this->qalinAnimatedBanner->animate();

        return new Navigate($selected::class);
    }

    private function cursorNext(): Stay
    {
        if ([] !== $this->filteredScreens) {
            $this->cursorIndex = ($this->cursorIndex + 1) % \count($this->filteredScreens);
        }

        return new Stay();
    }

    private function cursorPrevious(): Stay
    {
        if ([] !== $this->filteredScreens) {
            $count = \count($this->filteredScreens);
            $this->cursorIndex = ($this->cursorIndex - 1 + $count) % $count;
        }

        return new Stay();
    }
}
