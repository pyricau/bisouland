<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class <?= $class_name; ?> extends TestCase
{
    public function test_it_<?= $action_snake; ?>(): void
    {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/<?= $action_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
<?php foreach ($action_parameters as $param): ?>
                '<?= $param['name']; ?>' => 'valid_<?= $param['name']; ?>', // TODO: use fixture
<?php endforeach; ?>
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());
    }

<?php if (count($action_parameters) > 0): ?>
    /**
     * @param array<string, string> $body
     */
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/<?= $action_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($body, \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     body: array<string, string>,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
<?php foreach ($action_parameters as $i => $param): ?>
        yield [
            'scenario' => '<?= $param['name']; ?> as a required parameter',
            'body' => [<?php foreach ($action_parameters as $j => $otherParam): ?><?php if ($j !== $i): ?>'<?= $otherParam['name']; ?>' => 'valid_<?= $otherParam['name']; ?>', <?php endif; ?><?php endforeach; ?>],
        ];
<?php endforeach; ?>
    }

    /**
     * @param array<string, string> $body
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails on $scenario')]
    public function test_it_fails_on_invalid_input(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/<?= $action_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($body, \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     body: array<string, string>,
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
<?php foreach ($action_parameters as $param): ?>
        yield [
            'scenario' => 'invalid <?= $param['name']; ?>',
            'body' => [<?php foreach ($action_parameters as $otherParam): ?>'<?= $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']): ?>'x'<?php else: ?>'valid_<?= $otherParam['name']; ?>'<?php endif; ?>, <?php endforeach; ?>],
        ];
<?php endforeach; ?>
    }
<?php endif; ?>
}
