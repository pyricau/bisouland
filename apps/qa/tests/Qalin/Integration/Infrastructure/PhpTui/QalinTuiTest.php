<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\Infrastructure\PhpTui;

use Bl\Qa\Infrastructure\PhpTui\Action;
use Bl\Qa\Infrastructure\PhpTui\Action\Quit;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\QalinTui;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Small]
final class QalinTuiTest extends TestCase
{
    /**
     * @param class-string<Action> $expected
     */
    #[DataProvider('reportsProvider')]
    #[TestDox('It reports $scenario')]
    public function test_it_reports(string $scenario, Event $event, string $expected): void
    {
        $tui = TestKernelSingleton::get()->container()->get(QalinTui::class);
        $this->assertInstanceOf(QalinTui::class, $tui);

        $result = $tui->handle($event);

        $this->assertInstanceOf($expected, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     event: Event,
     *     expected: class-string<Action>,
     * }>
     */
    public static function reportsProvider(): \Iterator
    {
        yield [
            'scenario' => 'Quit when active screen returns Quit',
            'event' => CodedKeyEvent::new(KeyCode::Esc), // HomeScreen: Esc → Quit
            'expected' => Quit::class,
        ];
        yield [
            'scenario' => 'Stay when active screen returns Navigate (handles routing internally)',
            'event' => CodedKeyEvent::new(KeyCode::Enter), // HomeScreen: Enter → Navigate
            'expected' => Stay::class,
        ];
    }
}
