<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

<?php if ($has_username_param) { ?>
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
<?php } ?>
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
use <?php echo $param['fixture_fqcn']; ?>;
<?php } ?>
<?php } ?>
<?php if ($has_username_param) { ?>
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\PasswordPlainFixture;
<?php } ?>
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

#[CoversNothing]
#[Medium]
final class <?php echo $class_name; ?> extends TestCase
{
    public function test_it_runs_command_successfully(): void
    {
<?php if ($has_username_param) { ?>
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
<?php } ?>
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:<?php echo $action_kebab; ?>',
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
<?php if ('username' === $param['name']) { ?>
            '<?php echo $param['name']; ?>' => $username,
<?php } else { ?>
            '<?php echo $param['name']; ?>' => <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>(),
<?php } ?>
<?php } elseif ('int' === $param['type']) { ?>
            '<?php echo $param['name']; ?>' => 1, // TODO: use fixture
<?php } else { ?>
            '<?php echo $param['name']; ?>' => 'valid_<?php echo $param['name']; ?>', // TODO: use fixture
<?php } ?>
<?php } ?>
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
<?php foreach ($action_parameters as $i => $param) { ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as a required argument',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $j => $otherParam) { ?><?php if ($j !== $i) { ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?><?php } ?>],
            'expectedOutput' => '/missing.*<?php echo $param['name']; ?>/',
        ];
<?php } ?>
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
<?php if ($has_username_param) { ?>
        if ('invalid username' !== $scenario) {
            TestKernelSingleton::get()->actionRunner()->run(
                new SignUpNewPlayer((string) $input['username'], PasswordPlainFixture::makeString()),
            );
        }

<?php } ?>
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
<?php foreach ($action_parameters as $param) { ?>
        yield [
            'scenario' => 'invalid <?php echo $param['name']; ?>',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam) { ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']) { ?><?php echo 'int' === $otherParam['type'] ? '-1' : "'x'"; ?><?php } elseif ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>
<?php } ?>],
        ];
<?php } ?>
    }
}
