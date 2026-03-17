<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Action;

use Bl\Qa\Infrastructure\PhpTui\Action;

/**
 * Transition to another screen.
 *
 * Usage:
 *     return new Navigate(MyScreen::class);
 */
final readonly class Navigate implements Action
{
    public function __construct(
        public string $screen,
    ) {
    }
}
