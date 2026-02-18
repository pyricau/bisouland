<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>;
use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>Handler;
use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>Output;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
use <?php echo $param['fixture_fqcn']; ?>;
<?php } ?>
<?php } ?>
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(<?php echo $action_name; ?>Handler::class)]
#[Small]
final class <?php echo $class_name; ?> extends TestCase
{
    use ProphecyTrait;

    public function test_it_runs_action_successfully(): void
    {
        // TODO: set up test doubles and fixtures
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
        $<?php echo $param['name']; ?> = <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>();
<?php } elseif ('int' === $param['type']) { ?>
        $<?php echo $param['name']; ?> = 1; // TODO: use fixture
<?php } else { ?>
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>'; // TODO: use fixture
<?php } ?>
<?php } ?>

        $<?php echo $action_camel; ?>Handler = new <?php echo $action_name; ?>Handler(
            // TODO: inject revealed prophecies
        );
        $output = $<?php echo $action_camel; ?>Handler->run(new <?php echo $action_name; ?>(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], $action_parameters)); ?>));

        $this->assertInstanceOf(<?php echo $action_name; ?>Output::class, $output);
        // TODO: add assertions on $output->player
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    #[TestDox('It fails when $scenario')]
    #[DataProvider('failureProvider')]
    public function test_it_fails_when_an_error_occurs(
        string $scenario,
        string $exception,
    ): void {
        // TODO: set up test doubles
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
        $<?php echo $param['name']; ?> = <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>();
<?php } elseif ('int' === $param['type']) { ?>
        $<?php echo $param['name']; ?> = 1; // TODO: use fixture
<?php } else { ?>
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>'; // TODO: use fixture
<?php } ?>
<?php } ?>

        $<?php echo $action_camel; ?>Handler = new <?php echo $action_name; ?>Handler(
            // TODO: inject revealed prophecies
        );

        $this->expectException($exception);
        $<?php echo $action_camel; ?>Handler->run(new <?php echo $action_name; ?>(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], $action_parameters)); ?>));
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      exception: class-string<\Throwable>,
     *  }>
     */
    public static function failureProvider(): \Iterator
    {
        yield ['scenario' => 'validation fails', 'exception' => ValidationFailedException::class];
        yield ['scenario' => 'an unexpected error occurs', 'exception' => ServerErrorException::class];
    }
}
