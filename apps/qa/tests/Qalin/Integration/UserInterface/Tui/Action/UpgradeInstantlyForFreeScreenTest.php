<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Tui\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\UserInterface\Tui\Action\UpgradeInstantlyForFreeScreen;
use Bl\Qa\UserInterface\Tui\HomeScreen;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Medium]
final class UpgradeInstantlyForFreeScreenTest extends TestCase
{
    public function test_it_upgrades_instantly_for_free(): void
    {
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $screen = TestKernelSingleton::get()->container()->get(UpgradeInstantlyForFreeScreen::class);
        $this->assertInstanceOf(UpgradeInstantlyForFreeScreen::class, $screen);

        foreach (str_split($username) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → upgradable (default: first choice)
        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // upgradable → levels (default: 1)
        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // levels → Upgrade

        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter)); // submit

        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @param array{username: string, levels_override: string} $input
     */
    #[DataProvider('optionalFieldsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_optional_fields(
        string $scenario,
        array $input,
    ): void {
        $username = $input['username'];
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $screen = TestKernelSingleton::get()->container()->get(UpgradeInstantlyForFreeScreen::class);
        $this->assertInstanceOf(UpgradeInstantlyForFreeScreen::class, $screen);
        foreach (str_split($username) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // username → upgradable (default: first choice)
        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // upgradable → levels
        if ('' !== $input['levels_override']) {
            $screen->handle(CodedKeyEvent::new(KeyCode::Backspace)); // clear default '1'
            foreach (str_split($input['levels_override']) as $char) {
                $screen->handle(CharKeyEvent::new($char));
            }
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // levels → Upgrade
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));
        // submit
        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string, levels_override: string},
     * }>
     */
    public static function optionalFieldsProvider(): \Iterator
    {
        yield [
            'scenario' => 'levels as an optional field (defaults to 1)',
            'input' => ['username' => UsernameFixture::makeString(), 'levels_override' => ''],
        ];
        yield [
            'scenario' => 'levels as an optional field (set to 2)',
            'input' => ['username' => UsernameFixture::makeString(), 'levels_override' => '2'],
        ];
    }

    /**
     * @param array{username: string, upgradable_filter: string} $input
     */
    #[DataProvider('requiredFieldsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_fields(
        string $scenario,
        array $input,
    ): void {
        $screen = TestKernelSingleton::get()->container()->get(UpgradeInstantlyForFreeScreen::class);
        $this->assertInstanceOf(UpgradeInstantlyForFreeScreen::class, $screen);
        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // username → upgradable
        foreach (str_split($input['upgradable_filter']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // upgradable → levels
        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // levels → Upgrade
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));
        // submit
        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string, upgradable_filter: string},
     * }>
     */
    public static function requiredFieldsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required field',
            'input' => ['username' => '', 'upgradable_filter' => ''],
        ];
        yield [
            'scenario' => 'upgradable as a required field',
            'input' => ['username' => UsernameFixture::makeString(), 'upgradable_filter' => 'zzz'],
        ];
    }

    /**
     * @param array{username: string, upgradable_filter: string, levels_override: string} $input
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_on_invalid_input(
        string $scenario,
        bool $preCreate,
        array $input,
    ): void {
        if ($preCreate) {
            TestKernelSingleton::get()->actionRunner()->run(
                new SignUpNewPlayer($input['username'], PasswordPlainFixture::makeString()),
            );
        }

        $screen = TestKernelSingleton::get()->container()->get(UpgradeInstantlyForFreeScreen::class);
        $this->assertInstanceOf(UpgradeInstantlyForFreeScreen::class, $screen);
        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // username → upgradable
        foreach (str_split($input['upgradable_filter']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // upgradable → levels
        if ('' !== $input['levels_override']) {
            $screen->handle(CodedKeyEvent::new(KeyCode::Backspace)); // clear default '1'
            foreach (str_split($input['levels_override']) as $char) {
                $screen->handle(CharKeyEvent::new($char));
            }
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // levels → Upgrade
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));
        // submit
        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     preCreate: bool,
     *     input: array{username: string, upgradable_filter: string, levels_override: string},
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'preCreate' => false,
            'input' => ['username' => 'x', 'upgradable_filter' => '', 'levels_override' => ''],
        ];
        yield [
            'scenario' => 'invalid upgradable (ChoiceField: valid choices only)',
            'preCreate' => true,
            'input' => ['username' => UsernameFixture::makeString(), 'upgradable_filter' => 'zzz', 'levels_override' => ''],
        ];
        yield [
            'scenario' => 'invalid levels',
            'preCreate' => true,
            'input' => ['username' => UsernameFixture::makeString(), 'upgradable_filter' => '', 'levels_override' => '-1'],
        ];
    }

    #[TestDox('It reports Navigate to HomeScreen when pressing Esc')]
    public function test_it_reports_navigate_to_home_screen_on_esc(): void
    {
        $screen = TestKernelSingleton::get()->container()->get(UpgradeInstantlyForFreeScreen::class);
        $this->assertInstanceOf(UpgradeInstantlyForFreeScreen::class, $screen);

        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Esc));

        $this->assertInstanceOf(Navigate::class, $result);
        $this->assertSame(HomeScreen::class, $result->screen);
    }
}
