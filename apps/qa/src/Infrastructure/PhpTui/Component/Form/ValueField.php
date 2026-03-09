<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Form;

/**
 * A Field that contributes a named value to FormComponent::getValues().
 */
interface ValueField extends Field
{
    public function getLabel(): string;

    public function getValue(): string;
}
