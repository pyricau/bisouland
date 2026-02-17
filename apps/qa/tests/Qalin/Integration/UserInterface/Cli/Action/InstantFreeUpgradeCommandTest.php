<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Cli\Action;

use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordPlainFixture;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevels\UpgradableFixture;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

#[CoversNothing]
#[Medium]
final class InstantFreeUpgradeCommandTest extends TestCase
{
    public function test_it_runs_command_successfully(): void
    {
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:instant-free-upgrade',
            'username' => $username,
            'upgradable' => UpgradableFixture::makeString(),
            'levels' => 1, // TODO: use fixture
        ]);

        $this->assertSame(Command::SUCCESS, $application->getStatusCode());
    }

    /**
     * @param array<string, int|string> $input
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
     *     input: array<string, int|string>,
     *     expectedOutput: string,
     * }>
     */
    public static function argumentsAndOptionsProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required argument',
            'input' => ['command' => 'action:instant-free-upgrade', 'upgradable' => UpgradableFixture::makeString(), 'levels' => 1],
            'expectedOutput' => '/missing.*username/',
        ];
        yield [
            'scenario' => 'upgradable as a required argument',
            'input' => ['command' => 'action:instant-free-upgrade', 'username' => UsernameFixture::makeString(), 'levels' => 1],
            'expectedOutput' => '/missing.*upgradable/',
        ];
        yield [
            'scenario' => 'levels as a required argument',
            'input' => ['command' => 'action:instant-free-upgrade', 'username' => UsernameFixture::makeString(), 'upgradable' => UpgradableFixture::makeString()],
            'expectedOutput' => '/missing.*levels/',
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
            'input' => ['command' => 'action:instant-free-upgrade', 'username' => 'x', 'upgradable' => UpgradableFixture::makeString(), 'levels' => 1],
        ];
        yield [
            'scenario' => 'invalid upgradable',
            'input' => ['command' => 'action:instant-free-upgrade', 'username' => UsernameFixture::makeString(), 'upgradable' => 'x', 'levels' => 1],
        ];
        yield [
            'scenario' => 'invalid levels',
            'input' => ['command' => 'action:instant-free-upgrade', 'username' => UsernameFixture::makeString(), 'upgradable' => UpgradableFixture::makeString(), 'levels' => -1],
        ];
    }
}
