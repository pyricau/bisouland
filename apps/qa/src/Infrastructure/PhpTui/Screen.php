<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui;

use PhpTui\Term\Event;
use PhpTui\Tui\Widget\Widget;

/**
 * Represents a full-page view in the TUI application.
 *
 * Usage (implementing a Screen):
 *     final class MyScreen implements Screen
 *     {
 *         public function name(): string { return 'My Screen'; }
 *
 *         public function build(): Widget
 *         {
 *             return LayoutWidget::from($banner, $nav, $body, $footer);
 *         }
 *
 *         public function handle(Event $event): Action
 *         {
 *             if ($event instanceof CodedKeyEvent && KeyCode::Esc === $event->code) {
 *                 return new Navigate(HomeScreen::class);
 *             }
 *
 *             return new Stay(); // event handled internally, no navigation needed
 *         }
 *     }
 */
interface Screen
{
    /**
     * Unique display name for this screen (shown in menus, titles, etc.).
     */
    public function name(): string;

    /**
     * Renders current screen state as a Widget for the frame redraw.
     */
    public function build(): Widget;

    /**
     * Processes an input event and returns an Action to signal what should happen next:
     * - Stay:     event handled internally, stay on this screen
     * - Navigate: transition to another screen (identified by class name)
     * - Quit:     exit the TUI
     */
    public function handle(Event $event): Action;
}
