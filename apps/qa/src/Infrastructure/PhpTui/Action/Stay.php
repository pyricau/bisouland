<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Action;

use Bl\Qa\Infrastructure\PhpTui\Action;

/**
 * Event was handled internally by the screen; no navigation needed.
 *
 * Usage:
 *     return new Stay(); // stay on this screen
 */
final readonly class Stay implements Action
{
}
