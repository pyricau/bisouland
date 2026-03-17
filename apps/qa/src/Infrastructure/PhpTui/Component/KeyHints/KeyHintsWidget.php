<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\KeyHints;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\Constrained;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Layout\Constraint\LengthConstraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Widget;

/**
 * A widget that displays a row of keyboard shortcut key hints.
 *
 * Usage:
 *     $widget = KeyHintsWidget::from(['Next' => 'Tab', 'Submit' => 'Enter', 'Back' => 'Esc']);
 *
 *     // Styles can be customized:
 *     $widget = $widget->actionStyle($style)->keyStyle($style);
 */
final readonly class KeyHintsWidget implements Widget, Constrained
{
    /**
     * @param array<string, string> $keyHints action => key
     */
    private function __construct(
        public array $keyHints,
        public Style $actionStyle,
        public Style $keyStyle,
    ) {
    }

    /**
     * @param array<string, string> $keyHints action => key
     */
    public static function from(array $keyHints): self
    {
        foreach ($keyHints as $action => $key) {
            if ('' === $action) {
                throw ValidationFailedException::make(
                    'Invalid "KeyHintsWidget" parameter: action should not be empty (`\'\' => \''.$key."'` given)",
                );
            }

            if ('' === $key) {
                throw ValidationFailedException::make(
                    "Invalid \"KeyHintsWidget\" parameter: key should not be empty (`'{$action}' => ''` given)",
                );
            }
        }

        return new self(
            $keyHints,
            Style::default()->fg(AnsiColor::DarkGray),
            Style::default()->fg(AnsiColor::Blue)->addModifier(Modifier::BOLD),
        );
    }

    public function constraint(): LengthConstraint
    {
        return Constraint::length(3);
    }

    public function actionStyle(Style $actionStyle): self
    {
        return new self($this->keyHints, $actionStyle, $this->keyStyle);
    }

    public function keyStyle(Style $keyStyle): self
    {
        return new self($this->keyHints, $this->actionStyle, $keyStyle);
    }
}
