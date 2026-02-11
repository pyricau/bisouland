<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Cli\Action;

use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordPlainFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

#[CoversNothing]
#[Medium]
final class SignUpNewPlayerCommandTest extends TestCase
{
    public function test_it_signs_up_a_new_player(): void
    {
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:sign-up-new-player',
            'username' => UsernameFixture::makeString(),
            'password' => PasswordPlainFixture::makeString(),
        ]);

        $this->assertSame(Command::SUCCESS, $application->getStatusCode());
    }

    /**
     * @param array<string, string> $input
     */
    #[DataProvider('argumentsAndOptionsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_arguments_and_options(
        string $scenario,
        array $input,
        string $expectedOutput,
    ): void {
        $this->expectOutputRegex($expectedOutput);

        $application = TestKernelSingleton::get()->application();

        $application->run($input);

        $this->assertSame(Command::FAILURE, $application->getStatusCode());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array<string, string>,
     *     expectedOutput: string,
     * }>
     */
    public static function argumentsAndOptionsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required argument',
            'input' => ['command' => 'action:sign-up-new-player', 'password' => PasswordPlainFixture::makeString()],
            'expectedOutput' => '/missing.*username/',
        ];
        yield [
            'scenario' => 'password as a required argument',
            'input' => ['command' => 'action:sign-up-new-player', 'username' => UsernameFixture::makeString()],
            'expectedOutput' => '/missing.*password/',
        ];
    }

    /**
     * @param array<string, string> $input
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails on $scenario')]
    public function test_it_fails_on_invalid_arguments_and_options(
        string $scenario,
        array $input,
    ): void {
        $application = TestKernelSingleton::get()->application();

        $application->run($input);

        $this->assertSame(Command::INVALID, $application->getStatusCode());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array<string, string>,
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'input' => ['command' => 'action:sign-up-new-player', 'username' => 'usr', 'password' => PasswordPlainFixture::makeString()],
        ];
        yield [
            'scenario' => 'invalid password',
            'input' => ['command' => 'action:sign-up-new-player', 'username' => UsernameFixture::makeString(), 'password' => 'short'],
        ];
    }
}
