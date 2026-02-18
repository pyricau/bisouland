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
<?php $key = null !== $param['default'] ? '--'.$param['name'] : $param['name']; ?>
<?php if ($param['fixture_fqcn']) { ?>
<?php if ('username' === $param['name']) { ?>
            '<?php echo $key; ?>' => $username,
<?php } else { ?>
            '<?php echo $key; ?>' => <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>(),
<?php } ?>
<?php } elseif ('int' === $param['type']) { ?>
            '<?php echo $key; ?>' => 1, // TODO: use fixture
<?php } else { ?>
            '<?php echo $key; ?>' => 'valid_<?php echo $param['name']; ?>', // TODO: use fixture
<?php } ?>
<?php } ?>
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
<?php foreach ($action_parameters as $i => $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as a required argument',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $j => $otherParam) { ?><?php if ($j !== $i && null === $otherParam['default']) { ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?><?php } ?>],
            'expectedOutput' => '/missing.*<?php echo $param['name']; ?>/',
        ];
<?php } ?>
    }

<?php if ($has_optional_params) { ?>
    /**
     * @param array<string, int|string> $input
     */
    #[DataProvider('optionsProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_options(
        string $scenario,
        array $input,
    ): void {
<?php if ($has_username_param) { ?>
        $username = (string) $input['username'];
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
<?php } ?>
        $application = TestKernelSingleton::get()->application();

        $application->run($input);

        $this->assertSame(Command::SUCCESS, $application->getStatusCode());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     input: array<string, int|string>,
     * }>
     */
    public static function optionsProvider(): \Iterator
    {
<?php foreach ($action_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as an option (defaults to <?php echo $param['default']; ?>)',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam) {
                if (null !== $otherParam['default']) {
                    continue;
                } ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php if ('username' === $otherParam['name']) { ?>UsernameFixture::makeString()<?php } else { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } ?><?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?>],
        ];
        yield [
            'scenario' => '<?php echo $param['name']; ?> as an option (set to <?php echo 'int' === $param['type'] ? (int) $param['default'] + 1 : 'another_value'; ?>)',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam) {
                if (null !== $otherParam['default']) {
                    continue;
                } ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php if ('username' === $otherParam['name']) { ?>UsernameFixture::makeString()<?php } else { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } ?><?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?>, '--<?php echo $param['name']; ?>' => <?php echo 'int' === $param['type'] ? (int) $param['default'] + 1 : "'another_value'"; ?>],
        ];
<?php } ?>
    }

<?php } ?>
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
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam) { ?><?php $otherKey = null !== $otherParam['default'] ? '--'.$otherParam['name'] : $otherParam['name']; ?>, '<?php echo $otherKey; ?>' => <?php if ($otherParam['name'] === $param['name']) { ?><?php echo 'int' === $otherParam['type'] ? '-1' : "'x'"; ?><?php } elseif ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>
<?php } ?>],
        ];
<?php } ?>
    }
}
