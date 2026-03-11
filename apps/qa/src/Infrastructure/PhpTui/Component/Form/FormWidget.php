<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form;

use Bl\Exception\ValidationFailedException;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that stacks form items vertically.
 *
 * Usage:
 *     $widget = FormWidget::fromItems($inputFieldWidget, $submitButtonWidget);
 */
final readonly class FormWidget implements Widget
{
    /**
     * @param non-empty-list<Widget> $items
     */
    private function __construct(
        public array $items,
    ) {
    }

    public static function fromItems(Widget ...$items): self
    {
        if ([] === $items) {
            throw ValidationFailedException::make(
                'Invalid "FormWidget" parameter: items should not be empty (`[]` given)',
            );
        }

        return new self(array_values($items));
    }
}
