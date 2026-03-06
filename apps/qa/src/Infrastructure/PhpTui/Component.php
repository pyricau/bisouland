<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui;

use PhpTui\Term\Event;
use PhpTui\Tui\Widget\Widget;

/**
 * A reusable UI element that encapsulates state, event handling, and rendering.
 *
 * Inspired by the ratatui Component pattern (immediate-mode TUI):
 *   the main loop calls build() every frame to get the Widget tree,
 *   then polls for events and calls handle() which mutates internal state.
 *
 * build() creates a fresh stateless Widget from the component's current state,
 *   it is called every frame regardless of whether handle() changed anything
 *   (the renderer diffs the widget tree against the buffer).
 *
 * handle() returns a ComponentState to signal what happened.
 */
interface Component
{
    public function handle(Event $event): ComponentState;

    public function build(): Widget;
}
