<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTab;

enum HomeTab: string implements HotkeyTab
{
    case Actions = 'Actions';
    case Scenarios = 'Scenarios';

    public function key(): string
    {
        return match ($this) {
            self::Actions => '1',
            self::Scenarios => '2',
        };
    }

    public function label(): string
    {
        return $this->value;
    }
}
