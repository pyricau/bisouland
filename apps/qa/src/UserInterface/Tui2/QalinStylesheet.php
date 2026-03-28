<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui2;

use Symfony\Component\Tui\Style\Direction;
use Symfony\Component\Tui\Style\Padding;
use Symfony\Component\Tui\Style\Style;
use Symfony\Component\Tui\Style\TailwindStylesheet;

final readonly class QalinStylesheet
{
    public static function create(): TailwindStylesheet
    {
        $tailwindStylesheet = new TailwindStylesheet();
        $tailwindStylesheet->addRule(':root', new Style(padding: Padding::xy(0, 1)));
        $tailwindStylesheet->addRule('.logo', new Style(color: '#ed8796', flex: 0));
        $tailwindStylesheet->addRule('.banner', new Style(direction: Direction::Horizontal, gap: 3));
        $tailwindStylesheet->addRule('.slogan-title', new Style(bold: true));

        return $tailwindStylesheet;
    }
}
