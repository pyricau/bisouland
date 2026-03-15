<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Action;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Quit;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\Screen;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\UserInterface\Tui\Action\UpgradeInstantlyForFreeScreen;
use Bl\Qa\UserInterface\Tui\HomeScreen;
use Bl\Qa\UserInterface\Tui\Scenario\SignInNewPlayerScreen;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Small]
final class HomeScreenTest extends TestCase
{
    /**
     * @param list<Event>          $setupEvents
     * @param class-string<Screen> $expectedScreen
     */
    #[DataProvider('hasProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has(string $scenario, array $setupEvents, string $expectedScreen): void
    {
        $screen = TestKernelSingleton::get()->container()->get(HomeScreen::class);
        $this->assertInstanceOf(HomeScreen::class, $screen);

        foreach ($setupEvents as $setupEvent) {
            $screen->handle($setupEvent);
        }

        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));

        $this->assertInstanceOf(Navigate::class, $result);
        $this->assertSame($expectedScreen, $result->screen);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     setupEvents: list<Event>,
     *     expectedScreen: class-string<Screen>,
     * }>
     */
    public static function hasProvider(): \Iterator
    {
        yield [
            'scenario' => 'HotkeyTabs to switch between Actions and Scenarios',
            'setupEvents' => [CharKeyEvent::new('2')],
            'expectedScreen' => SignInNewPlayerScreen::class,
        ];
        yield [
            'scenario' => 'ChoiceInput to navigate to screens',
            'setupEvents' => array_map(
                static fn (string $char): CharKeyEvent => CharKeyEvent::new($char),
                str_split('Upgrade'),
            ),
            'expectedScreen' => UpgradeInstantlyForFreeScreen::class,
        ];
    }

    /**
     * @param non-empty-list<Event> $events
     * @param class-string<Action>  $expected
     */
    #[DataProvider('reportsProvider')]
    #[TestDox('It reports $scenario')]
    public function test_it_reports(string $scenario, array $events, string $expected): void
    {
        $screen = TestKernelSingleton::get()->container()->get(HomeScreen::class);
        $this->assertInstanceOf(HomeScreen::class, $screen);

        $result = null;
        foreach ($events as $event) {
            $result = $screen->handle($event);
        }

        $this->assertInstanceOf($expected, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     events: non-empty-list<Event>,
     *     expected: class-string<Action>,
     * }>
     */
    public static function reportsProvider(): \Iterator
    {
        yield [
            'scenario' => 'Quit on Esc',
            'events' => [CodedKeyEvent::new(KeyCode::Esc)],
            'expected' => Quit::class,
        ];
        yield [
            'scenario' => 'Navigate on Enter',
            'events' => [CodedKeyEvent::new(KeyCode::Enter)],
            'expected' => Navigate::class,
        ];
        yield [
            'scenario' => 'Stay on Enter, when ChoiceField is not valid',
            'events' => [
                CharKeyEvent::new('z'),
                CharKeyEvent::new('z'),
                CharKeyEvent::new('z'),
                CodedKeyEvent::new(KeyCode::Enter),
            ],
            'expected' => Stay::class,
        ];
    }
}
