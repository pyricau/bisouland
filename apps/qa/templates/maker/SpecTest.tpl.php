<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(<?php echo $action_name; ?>::class)]
#[Small]
final class <?php echo $class_name; ?> extends TestCase
{
    use ProphecyTrait;

    public function test_it_<?php echo $action_snake; ?>(): void
    {
        // TODO: set up test doubles and fixtures
<?php foreach ($action_parameters as $param) { ?>
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>';
<?php } ?>

        $<?php echo $action_camel; ?> = new <?php echo $action_name; ?>(
            // TODO: inject revealed prophecies
        );
        $<?php echo $action_camel; ?>->run(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], $action_parameters)); ?>);

        // TODO: add assertions
        $this->assertTrue(true);
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
        $<?php echo $param['name']; ?> = 'valid_<?php echo $param['name']; ?>';
<?php } ?>

        $<?php echo $action_camel; ?> = new <?php echo $action_name; ?>(
            // TODO: inject revealed prophecies
        );

        $this->expectException($exception);
        $<?php echo $action_camel; ?>->run(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], $action_parameters)); ?>);
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
