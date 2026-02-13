<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
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
    public function test_it_<?php echo $action_snake; ?>(): void
    {
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:<?php echo $action_kebab; ?>',
<?php foreach ($action_parameters as $param) { ?>
            '<?php echo $param['name']; ?>' => 'valid_<?php echo $param['name']; ?>', // TODO: use fixture
<?php } ?>
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
<?php foreach ($action_parameters as $i => $param) { ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as a required argument',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $j => $otherParam) { ?><?php if ($j !== $i) { ?>, '<?php echo $otherParam['name']; ?>' => 'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?>],
            'expectedOutput' => '/missing.*<?php echo $param['name']; ?>/',
        ];
<?php } ?>
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
<?php foreach ($action_parameters as $param) { ?>
        yield [
            'scenario' => 'invalid <?php echo $param['name']; ?>',
            'input' => ['command' => 'action:<?php echo $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam) { ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']) { ?>'x'<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>
<?php } ?>],
        ];
<?php } ?>
    }
}
