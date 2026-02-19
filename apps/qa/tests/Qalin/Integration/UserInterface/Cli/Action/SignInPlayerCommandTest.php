<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Cli\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

#[CoversNothing]
#[Medium]
final class SignInPlayerCommandTest extends TestCase
{
    public function test_it_runs_command_successfully(): void
    {
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:sign-in-player',
            'username' => $username,
        ]);

        $this->assertSame(Command::SUCCESS, $application->getStatusCode());
    }

    /**
     * @param array<string, int|string> $input
     */
    #[DataProvider('requiredArgumentsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_arguments(
        string $scenario,
        array $input,
        string $expectedOutput,
    ): void {
        $application = TestKernelSingleton::get()->application();

        $application->run($input);

        $this->assertSame(Command::FAILURE, $application->getStatusCode());
        $this->assertMatchesRegularExpression($expectedOutput, $application->getErrorOutput());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array<string, int|string>,
     *     expectedOutput: string,
     * }>
     */
    public static function requiredArgumentsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required argument',
            'input' => ['command' => 'action:sign-in-player'],
            'expectedOutput' => '/missing.*username/',
        ];
    }

    /**
     * @param array<string, int|string> $input
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails on $scenario')]
    public function test_it_fails_on_invalid_arguments_and_options(
        string $scenario,
        array $input,
    ): void {
        if ('invalid username' !== $scenario) {
            TestKernelSingleton::get()->actionRunner()->run(
                new SignUpNewPlayer((string) $input['username'], PasswordPlainFixture::makeString()),
            );
        }

        $application = TestKernelSingleton::get()->application();

        $application->run($input);

        $this->assertSame(Command::INVALID, $application->getStatusCode());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array<string, int|string>,
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'input' => ['command' => 'action:sign-in-player', 'username' => 'x'],
        ];
    }
}
