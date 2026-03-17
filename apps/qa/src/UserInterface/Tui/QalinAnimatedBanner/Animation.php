<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui\QalinAnimatedBanner;

use PhpTui\Tui\Style\Style;

interface Animation
{
    public function animate(): void;

    /**
     * @return list<string>
     */
    public function logo(): array;

    public function logoStyle(): Style;
}
