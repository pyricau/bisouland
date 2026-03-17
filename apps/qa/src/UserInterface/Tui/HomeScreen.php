<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Action;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Quit;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use Bl\Qa\Infrastructure\PhpTui\Screen;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
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

    private ChoiceFieldComponent $choiceInput;

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
        $this->choiceInput = $this->buildChoiceInput();
    }

    public function name(): string
    {
        return 'Home';
    }

    public function build(): Widget
    {
        return LayoutWidget::from(
            $this->qalinAnimatedBanner->widget(),
            $this->tabs->build(),
            $this->choiceInput->build(),
            KeyHintsWidget::from(['Next' => 'Tab', 'Select' => 'Enter', 'Quit' => 'Esc']),
        );
    }

    public function handle(Event $event): Action
    {
        return match (true) {
            $event instanceof CodedKeyEvent => match ($event->code) {
                KeyCode::Esc => new Quit(),
                KeyCode::Tab, KeyCode::Down => $this->handleChoiceInput(CodedKeyEvent::new(KeyCode::Down)),
                KeyCode::BackTab, KeyCode::Up => $this->handleChoiceInput(CodedKeyEvent::new(KeyCode::Up)),
                KeyCode::Enter => $this->selectCurrentScreen(),
                default => $this->handleChoiceInput($event),
            },
            $event instanceof CharKeyEvent => match ($this->tabs->handle($event)) {
                ComponentState::Changed => $this->resetChoiceInput(),
                ComponentState::Handled => new Stay(),
                ComponentState::Ignored, ComponentState::Submitted => $this->handleChoiceInput($event),
            },
            default => new Stay(),
        };
    }

    private function activeTab(): HomeTab
    {
        return $this->tabs->isFocused();
    }

    private function buildChoiceInput(): ChoiceFieldComponent
    {
        $screens = match ($this->activeTab()) {
            HomeTab::Actions => $this->actions,
            HomeTab::Scenarios => $this->scenarios,
        };
        $choices = array_map(static fn (Screen $screen): string => $screen->name(), $screens);
        $component = ChoiceFieldComponent::fromLabelAndChoices($this->activeTab()->label(), $choices);
        $component->focus();

        return $component;
    }

    private function handleChoiceInput(Event $event): Stay
    {
        $this->choiceInput->handle($event);

        return new Stay();
    }

    private function resetChoiceInput(): Stay
    {
        $this->choiceInput = $this->buildChoiceInput();

        return new Stay();
    }

    private function selectCurrentScreen(): Action
    {
        $selectedName = $this->choiceInput->getValue();

        if ('' === $selectedName) {
            return new Stay();
        }

        $screens = match ($this->activeTab()) {
            HomeTab::Actions => $this->actions,
            HomeTab::Scenarios => $this->scenarios,
        };

        foreach ($screens as $screen) {
            if ($screen->name() === $selectedName) {
                $this->qalinAnimatedBanner->animate();

                return new Navigate($screen::class);
            }
        }

        return new Stay();
    }
}
