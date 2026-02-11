<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

#[CoversNothing]
#[Medium]
final class <?= $class_name; ?> extends TestCase
{
    public function test_it_<?= $action_snake; ?>(): void
    {
        $application = TestKernelSingleton::get()->application();

        $application->run([
            'command' => 'action:<?= $action_kebab; ?>',
<?php foreach ($action_parameters as $param): ?>
            '<?= $param['name']; ?>' => 'valid_<?= $param['name']; ?>', // TODO: use fixture
<?php endforeach; ?>
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
<?php foreach ($action_parameters as $i => $param): ?>
        yield [
            'scenario' => '<?= $param['name']; ?> as a required argument',
            'input' => ['command' => 'action:<?= $action_kebab; ?>'<?php foreach ($action_parameters as $j => $otherParam): ?><?php if ($j !== $i): ?>, '<?= $otherParam['name']; ?>' => 'valid_<?= $otherParam['name']; ?>'<?php endif; ?><?php endforeach; ?>],
            'expectedOutput' => '/missing.*<?= $param['name']; ?>/',
        ];
<?php endforeach; ?>
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
<?php foreach ($action_parameters as $param): ?>
        yield [
            'scenario' => 'invalid <?= $param['name']; ?>',
            'input' => ['command' => 'action:<?= $action_kebab; ?>'<?php foreach ($action_parameters as $otherParam): ?>, '<?= $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']): ?>'x'<?php else: ?>'valid_<?= $otherParam['name']; ?>'<?php endif; ?>
<?php endforeach; ?>],
        ];
<?php endforeach; ?>
    }
}
