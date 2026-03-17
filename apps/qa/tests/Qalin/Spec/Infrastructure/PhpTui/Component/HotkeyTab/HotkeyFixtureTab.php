<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\HotkeyTab;

use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTab;

enum HotkeyFixtureTab: string implements HotkeyTab
{
    case TabA = 'TabA';
    case TabB = 'TabB';
    case TabC = 'TabC';

    public function key(): string
    {
        return match ($this) {
            self::TabA => '1',
            self::TabB => '2',
            self::TabC => '3',
        };
    }

    public function label(): string
    {
        return $this->value;
    }
}
