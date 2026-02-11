<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Application\Action\<?= $action_name; ?>;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(<?= $action_name; ?>::class)]
#[Small]
final class <?= $class_name; ?> extends TestCase
{
    use ProphecyTrait;

    public function test_it_<?= $action_snake; ?>(): void
    {
        // TODO: set up test doubles and fixtures
<?php foreach ($action_parameters as $param): ?>
        $<?= $param['name']; ?> = 'valid_<?= $param['name']; ?>';
<?php endforeach; ?>

        $<?= $action_camel; ?> = new <?= $action_name; ?>(
            // TODO: inject revealed prophecies
        );
        $<?= $action_camel; ?>->run(<?= implode(', ', array_map(fn ($p) => '$' . $p['name'], $action_parameters)); ?>);

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
<?php foreach ($action_parameters as $param): ?>
        $<?= $param['name']; ?> = 'valid_<?= $param['name']; ?>';
<?php endforeach; ?>

        $<?= $action_camel; ?> = new <?= $action_name; ?>(
            // TODO: inject revealed prophecies
        );

        $this->expectException($exception);
        $<?= $action_camel; ?>->run(<?= implode(', ', array_map(fn ($p) => '$' . $p['name'], $action_parameters)); ?>);
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
