<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form;

use Bl\Qa\Infrastructure\PhpTui\Component;

/**
 * A focusable form element: a Component that can be focused and unfocused by the Form.
 */
interface Field extends Component
{
    public function focus(): void;

    public function unfocus(): void;

    public function isFocused(): bool;
}
