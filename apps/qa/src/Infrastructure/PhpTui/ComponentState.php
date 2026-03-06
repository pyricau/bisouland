<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui;

/**
 * Returned by Component::handle() to signal what happened.
 *
 * Ignored: the event was not relevant to this component (e.g. unregistered key).
 * Handled: the event was recognized but did not change state (e.g. pressing the already focused tab).
 * Changed: the event caused a state change (e.g. switching to another tab).
 */
enum ComponentState
{
    case Ignored;
    case Handled;
    case Changed;
}
