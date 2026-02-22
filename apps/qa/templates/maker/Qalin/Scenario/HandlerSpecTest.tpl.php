<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Scenario\<?php echo $scenario_name; ?>\<?php echo $scenario_name; ?>;
use Bl\Qa\Application\Scenario\<?php echo $scenario_name; ?>\<?php echo $scenario_name; ?>Handler;
use Bl\Qa\Application\Scenario\<?php echo $scenario_name; ?>\<?php echo $scenario_output_name; ?>;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
<?php foreach ($scenario_parameters as $param) { ?>
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

#[CoversClass(<?php echo $scenario_name; ?>Handler::class)]
#[Small]
final class <?php echo $class_name; ?> extends TestCase
{
    use ProphecyTrait;

    public function test_it_runs_scenario_successfully(): void
    {
        // TODO: set up prophecy mocks for action handler dependencies
<?php foreach ($scenario_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
        $<?php echo $param['name']; ?> = <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>();
<?php } elseif ('int' === $param['type']) { ?>
        $<?php echo $param['name']; ?> = 1; // TODO: use fixture
<?php } else { ?>
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>'; // TODO: use fixture
<?php } ?>
<?php } ?>

        $<?php echo $scenario_camel; ?>Handler = new <?php echo $scenario_name; ?>Handler(
            // TODO: inject revealed prophecies
        );
        $<?php echo lcfirst($scenario_output_name); ?> = $<?php echo $scenario_camel; ?>Handler->run(new <?php echo $scenario_name; ?>(
<?php foreach ($scenario_parameters as $param) { ?>
            $<?php echo $param['name']; ?>,
<?php } ?>
        ));

        $this->assertInstanceOf(<?php echo $scenario_output_name; ?>::class, $<?php echo lcfirst($scenario_output_name); ?>);
        // TODO: add assertions on $<?php echo lcfirst($scenario_output_name); ?>
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
        // TODO: set up test doubles that throw $exception
<?php foreach ($scenario_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
        $<?php echo $param['name']; ?> = <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>();
<?php } elseif ('int' === $param['type']) { ?>
        $<?php echo $param['name']; ?> = 1; // TODO: use fixture
<?php } else { ?>
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>'; // TODO: use fixture
<?php } ?>
<?php } ?>

        $<?php echo $scenario_camel; ?>Handler = new <?php echo $scenario_name; ?>Handler(
            // TODO: inject revealed prophecies
        );

        $this->expectException($exception);
        $<?php echo $scenario_camel; ?>Handler->run(new <?php echo $scenario_name; ?>(
<?php foreach ($scenario_parameters as $param) { ?>
            $<?php echo $param['name']; ?>,
<?php } ?>
        ));
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      exception: class-string<\Throwable>,
     *  }>
     */
    public static function failureProvider(): \Iterator
    {
        yield [
            'scenario' => 'validation fails',
            'exception' => ValidationFailedException::class,
        ];
        yield [
            'scenario' => 'an unexpected error occurs',
            'exception' => ServerErrorException::class,
        ];
    }
}
