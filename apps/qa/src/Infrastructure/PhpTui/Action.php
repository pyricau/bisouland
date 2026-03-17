<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui;

/**
 * Returned by {@see Screen::handle()} to signal what should happen next.
 *
 * Possible values:
 * - {@see Action\Stay}     event handled internally, stay on this screen
 * - {@see Action\Navigate} transition to another screen (with screen FQCN)
 * - {@see Action\Quit}     exit the TUI
 */
interface Action
{
}
