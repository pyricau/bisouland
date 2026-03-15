<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui;

use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Quit;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\ConstrainedWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor\LineEditorWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\ChoiceField\ChoiceFieldWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyValue\KeyValueWidgetRenderer;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidgetRenderer;
use Bl\Qa\UserInterface\Tui\HomeScreen;
use PhpTui\Term\Actions;
use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Bridge\PhpTerm\PhpTermBackend;
use PhpTui\Tui\DisplayBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[Autoconfigure(public: true, shared: false)]
final class QalinTui
{
    /** @var array<class-string<Screen>, Screen> */
    private readonly array $screens;

    private Screen $activeScreen;

    /**
     * @param iterable<Screen> $actionScreens
     * @param iterable<Screen> $scenarioScreens
     */
    public function __construct(
        HomeScreen $homeScreen,
        #[AutowireIterator('app.tui_action_screen')]
        iterable $actionScreens,
        #[AutowireIterator('app.tui_scenario_screen')]
        iterable $scenarioScreens,
    ) {
        $screens = [HomeScreen::class => $homeScreen];
        foreach ($actionScreens as $actionScreen) {
            $screens[$actionScreen::class] = $actionScreen;
        }

        foreach ($scenarioScreens as $scenarioScreen) {
            $screens[$scenarioScreen::class] = $scenarioScreen;
        }

        $this->screens = $screens;
        $this->activeScreen = $this->screens[HomeScreen::class];
    }

    public function handle(Event $event): Action
    {
        $action = $this->activeScreen->handle($event);

        if ($action instanceof Navigate) {
            $this->activeScreen = $this->screens[$action->screen] ?? $this->activeScreen;

            return new Stay();
        }

        return $action;
    }

    public function run(): void
    {
        $terminal = Terminal::new();
        $backend = PhpTermBackend::new($terminal);
        $display = DisplayBuilder::default($backend)
            ->addWidgetRenderer(new LayoutWidgetRenderer())
            ->addWidgetRenderer(new ConstrainedWidgetRenderer())
            ->addWidgetRenderer(new BannerWidgetRenderer())
            ->addWidgetRenderer(new KeyHintsWidgetRenderer())
            ->addWidgetRenderer(new FormWidgetRenderer())
            ->addWidgetRenderer(new HotkeyTabsWidgetRenderer())
            ->addWidgetRenderer(new ChoiceFieldWidgetRenderer())
            ->addWidgetRenderer(new InputFieldWidgetRenderer())
            ->addWidgetRenderer(new LineEditorWidgetRenderer())
            ->addWidgetRenderer(new KeyValueWidgetRenderer())
            ->addWidgetRenderer(new SubmitFieldWidgetRenderer())
            ->fullscreen()
            ->build()
        ;

        try {
            // hide the cursor
            $terminal->execute(Actions::cursorHide());
            // switch to the "alternate" screen so that we can return the user where they left off
            $terminal->execute(Actions::alternateScreenEnable());
            // enable "raw" mode to remove default terminal behavior (e.g. echoing key presses)
            $terminal->enableRawMode();

            while (true) {
                // Drain all queued events before redrawing, to avoid redundant redraws
                while ($event = $terminal->events()->next()) {
                    $action = $this->handle($event);
                    if ($action instanceof Quit) {
                        return;
                    }
                }

                $display->draw($this->activeScreen->build());
                // sleep for X ms, because we're not using true async lib
                usleep(50_000);
            }
        } finally {
            $terminal->disableRawMode();
            $terminal->execute(Actions::alternateScreenDisable());
            $terminal->execute(Actions::cursorShow());
        }
    }
}
