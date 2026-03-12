<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\KeyValue;

use Bl\Exception\ValidationFailedException;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a list of key-value pairs.
 *
 * Usage:
 *     $widget = KeyValueWidget::fromRows(['plan' => 'so cunning you can put a tail on it and call it a weasel']);
 *
 *     // The key style can be customized:
 *     $widget = $widget->keyStyle($style);
 */
final readonly class KeyValueWidget implements Widget
{
    /**
     * @param array<string, int|string> $rows
     */
    private function __construct(
        public array $rows,
        public Style $keyStyle,
    ) {
    }

    /**
     * @param array<string, int|string> $rows
     */
    public static function fromRows(array $rows): self
    {
        foreach (array_keys($rows) as $key) {
            if ('' === $key) {
                throw ValidationFailedException::make(
                    "Invalid \"KeyValueWidget\" parameter: key should not be empty (`'' => '{$rows[$key]}'` given)",
                );
            }
        }

        return new self($rows, Style::default()->fg(AnsiColor::Cyan));
    }

    public function keyStyle(Style $keyStyle): self
    {
        return new self($this->rows, $keyStyle);
    }
}
