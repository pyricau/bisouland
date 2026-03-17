<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Tui\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\UserInterface\Tui\Action\SignInPlayerScreen;
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
final class SignInPlayerScreenTest extends TestCase
{
    public function test_it_signs_in_a_player(): void
    {
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $screen = TestKernelSingleton::get()->container()->get(SignInPlayerScreen::class);
        $this->assertInstanceOf(SignInPlayerScreen::class, $screen);

        foreach (str_split($username) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → Sign In
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter)); // submit

        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @param array{username: string} $input
     */
    #[DataProvider('requiredFieldsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_fields(string $scenario, array $input): void
    {
        $screen = TestKernelSingleton::get()->container()->get(SignInPlayerScreen::class);
        $this->assertInstanceOf(SignInPlayerScreen::class, $screen);

        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → Sign In
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter)); // submit

        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string},
     * }>
     */
    public static function requiredFieldsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required field',
            'input' => ['username' => ''],
        ];
    }

    /**
     * @param array{username: string} $input
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_on_invalid_input(string $scenario, array $input): void
    {
        $screen = TestKernelSingleton::get()->container()->get(SignInPlayerScreen::class);
        $this->assertInstanceOf(SignInPlayerScreen::class, $screen);

        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → Sign In
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter)); // submit

        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string},
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'input' => ['username' => 'x'],
        ];
    }

    #[TestDox('It reports Navigate to HomeScreen when pressing Esc')]
    public function test_it_reports_navigate_to_home_screen_on_esc(): void
    {
        $screen = TestKernelSingleton::get()->container()->get(SignInPlayerScreen::class);
        $this->assertInstanceOf(SignInPlayerScreen::class, $screen);

        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Esc));

        $this->assertInstanceOf(Navigate::class, $result);
        $this->assertSame(HomeScreen::class, $result->screen);
    }
}
