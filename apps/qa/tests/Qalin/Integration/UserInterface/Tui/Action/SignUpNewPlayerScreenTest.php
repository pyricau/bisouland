<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Tui\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Bl\Qa\UserInterface\Tui\Action\SignUpNewPlayerScreen;
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
final class SignUpNewPlayerScreenTest extends TestCase
{
    public function test_it_signs_up_a_new_player(): void
    {
        $screen = TestKernelSingleton::get()->container()->get(SignUpNewPlayerScreen::class);
        $this->assertInstanceOf(SignUpNewPlayerScreen::class, $screen);
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();

        foreach (str_split($username) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // username → password
        foreach (str_split($password) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab)); // password → Sign Up
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter)); // submit

        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @param array{username: string, password: string} $input
     */
    #[DataProvider('requiredFieldsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_fields(
        string $scenario,
        array $input,
    ): void {
        $screen = TestKernelSingleton::get()->container()->get(SignUpNewPlayerScreen::class);
        $this->assertInstanceOf(SignUpNewPlayerScreen::class, $screen);
        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // username → password
        foreach (str_split($input['password']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // password → Sign Up
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));
        // submit
        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string, password: string},
     * }>
     */
    public static function requiredFieldsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required field',
            'input' => ['username' => '', 'password' => PasswordPlainFixture::makeString()],
        ];
        yield [
            'scenario' => 'password as a required field',
            'input' => ['username' => UsernameFixture::makeString(), 'password' => ''],
        ];
    }

    /**
     * @param array{username: string, password: string} $input
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails when $scenario')]
    public function test_it_fails_on_invalid_input(
        string $scenario,
        array $input,
    ): void {
        $screen = TestKernelSingleton::get()->container()->get(SignUpNewPlayerScreen::class);
        $this->assertInstanceOf(SignUpNewPlayerScreen::class, $screen);
        foreach (str_split($input['username']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // username → password
        foreach (str_split($input['password']) as $char) {
            $screen->handle(CharKeyEvent::new($char));
        }

        $screen->handle(CodedKeyEvent::new(KeyCode::Tab));
        // password → Sign Up
        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Enter));
        // submit
        $this->assertInstanceOf(Stay::class, $result);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array{username: string, password: string},
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'input' => ['username' => 'usr', 'password' => PasswordPlainFixture::makeString()],
        ];
        yield [
            'scenario' => 'invalid password',
            'input' => ['username' => UsernameFixture::makeString(), 'password' => 'short'],
        ];
    }

    #[TestDox('It reports Navigate to HomeScreen when pressing Esc')]
    public function test_it_reports_navigate_to_home_screen_on_esc(): void
    {
        $screen = TestKernelSingleton::get()->container()->get(SignUpNewPlayerScreen::class);
        $this->assertInstanceOf(SignUpNewPlayerScreen::class, $screen);

        $result = $screen->handle(CodedKeyEvent::new(KeyCode::Esc));

        $this->assertInstanceOf(Navigate::class, $result);
        $this->assertSame(HomeScreen::class, $result->screen);
    }
}
