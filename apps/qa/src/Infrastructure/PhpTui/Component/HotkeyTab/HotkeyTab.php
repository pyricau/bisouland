<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab;

interface HotkeyTab
{
    public function key(): string;

    public function label(): string;
}
