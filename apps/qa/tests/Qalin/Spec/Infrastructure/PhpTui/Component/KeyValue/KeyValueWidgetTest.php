<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\KeyValue;

use Bl\Exception\ValidationFailedException;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyValue\KeyValueWidget;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(KeyValueWidget::class)]
#[Small]
final class KeyValueWidgetTest extends TestCase
{
    /** @param array<string, int|string> $rows */
    #[DataProvider('rowsProvider')]
    #[TestDox('It has rows: $scenario')]
    public function test_it_has_rows(
        string $scenario,
        array $rows,
    ): void {
        $widget = KeyValueWidget::fromRows($rows);
        $this->assertSame($rows, $widget->rows);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     rows: array<string, int|string>,
     * }>
     */
    public static function rowsProvider(): \Generator
    {
        yield [
            'scenario' => 'empty (`[]`)',
            'rows' => [],
        ];
        yield [
            'scenario' => "one as `['menu' => 'rat au van']` (`[key => value]`)",
            'rows' => ['menu' => 'rat au van'],
        ];
        yield [
            'scenario' => "with empty value (e.g. `['menu' => '']`)",
            'rows' => ['menu' => ''],
        ];
        yield [
            'scenario' => "many as `['plan' => 'rotten borough', 'lucky' => 'lucky us, cluck, cluck!']` (`[key => value]`)",
            'rows' => ['plan' => 'rotten borough', 'lucky' => 'lucky us, cluck, cluck!'],
        ];
    }

    #[TestDox("It fails when key is empty (`['' => 'rat au van']` given)")]
    public function test_it_fails_when_key_is_empty(): void
    {
        $this->expectException(ValidationFailedException::class);

        KeyValueWidget::fromRows(['' => 'rat au van']);
    }

    #[DataProvider('defaultStylesProvider')]
    #[TestDox('It has default style: $scenario')]
    public function test_it_has_default_style(
        string $scenario,
        string $property,
        Style $expectedStyle,
    ): void {
        $widget = KeyValueWidget::fromRows([]);

        $this->assertEquals($expectedStyle, $widget->$property);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     property: string,
     *     expectedStyle: Style,
     * }>
     */
    public static function defaultStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'keyStyle in cyan',
            'property' => 'keyStyle',
            'expectedStyle' => Style::default()->fg(AnsiColor::Cyan),
        ];
    }

    #[DataProvider('customStylesProvider')]
    #[TestDox('It can customize style: $scenario')]
    public function test_it_can_customize_style(
        string $scenario,
        string $method,
        string $property,
    ): void {
        $customStyle = Style::default()->fg(AnsiColor::Red);

        $widget = KeyValueWidget::fromRows([])
            ->$method($customStyle);

        $this->assertSame($customStyle, $widget->$property);
    }

    /**
     * @return \Generator<array{
     *     scenario: string,
     *     method: string,
     *     property: string,
     * }>
     */
    public static function customStylesProvider(): \Generator
    {
        yield [
            'scenario' => 'keyStyle',
            'method' => 'keyStyle',
            'property' => 'keyStyle',
        ];
    }
}
