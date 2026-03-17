<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Editor\LineEditor;

use Bl\Exception\ValidationFailedException;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a text value with a cursor.
 *
 * Usage:
 *     $widget = LineEditorWidget::empty()
 *         ->value('baldrick')  // moves cursor to end
 *         ->cursorPosition(3)    // override cursor position
 *         ->focused()            // show REVERSED cursor char on render
 *     ;
 */
final readonly class LineEditorWidget implements Widget
{
    /**
     * @param int<0, max> $cursorPosition
     */
    private function __construct(
        public string $value,
        public int $cursorPosition,
        public bool $focused,
    ) {
    }

    public static function empty(): self
    {
        return new self('', 0, false);
    }

    public function value(string $value): self
    {
        return new self($value, mb_strlen($value), $this->focused);
    }

    public function cursorPosition(int $cursorPosition): self
    {
        $max = mb_strlen($this->value);
        if ($cursorPosition < 0 || $cursorPosition > $max) {
            throw ValidationFailedException::make(
                "Invalid \"LineEditorWidget\" parameter: cursorPosition should be within the value bounds (`{$cursorPosition}` given, min `0`, max `{$max}`)",
            );
        }

        return new self($this->value, $cursorPosition, $this->focused);
    }

    public function focused(): self
    {
        return new self($this->value, $this->cursorPosition, true);
    }

    public function unfocused(): self
    {
        return new self($this->value, $this->cursorPosition, false);
    }
}
