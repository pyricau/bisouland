<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Form;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldWidget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormWidget::class)]
#[Small]
final class FormWidgetTest extends TestCase
{
    #[TestDox('It has items (e.g. InputFieldWidget, SubmitFieldWidget)')]
    public function test_it_has_items(): void
    {
        $inputField = InputFieldWidget::fromLabel('Username');
        $submitField = SubmitFieldWidget::fromLabel('Submit');

        $widget = FormWidget::fromItems($inputField, $submitField);

        $this->assertSame($inputField, $widget->items[0]);
        $this->assertSame($submitField, $widget->items[1]);
    }

    #[TestDox('It fails when items is empty (`[]` given)')]
    public function test_it_fails_when_items_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        FormWidget::fromItems();
    }
}
