<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
<?php foreach ($scenario_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
use <?php echo $param['fixture_fqcn']; ?>;
<?php } ?>
<?php } ?>
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class <?php echo $class_name; ?> extends TestCase
{
    public function test_it_runs_scenario_successfully(): void
    {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/scenarios/<?php echo $scenario_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
<?php foreach ($scenario_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
                '<?php echo $param['name']; ?>' => <?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>(),
<?php } elseif ('int' === $param['type']) { ?>
                '<?php echo $param['name']; ?>' => 1, // TODO: use fixture
<?php } else { ?>
                '<?php echo $param['name']; ?>' => 'valid_<?php echo $param['name']; ?>', // TODO: use fixture
<?php } ?>
<?php } ?>
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());
    }

<?php if (count($scenario_parameters) > 0) { ?>
    /**
     * @param array<string, int|string> $body
     */
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/scenarios/<?php echo $scenario_kebab; ?>',
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
     *     body: array<string, int|string>,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
<?php foreach ($scenario_parameters as $i => $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as a required parameter',
            'body' => [<?php foreach ($scenario_parameters as $j => $otherParam) { ?><?php if ($j !== $i && null === $otherParam['default']) { ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?><?php } ?>],
        ];
<?php } ?>
    }

<?php if ($has_optional_params) { ?>
    /**
     * @param array<string, int|string> $body
     */
    #[DataProvider('optionalParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_optional_parameters(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/scenarios/<?php echo $scenario_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($body, \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     body: array<string, int|string>,
     * }>
     */
    public static function optionalParametersProvider(): \Iterator
    {
<?php foreach ($scenario_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as an optional parameter (defaults to <?php echo $param['default']; ?>)',
            'body' => [<?php foreach ($scenario_parameters as $otherParam) {
                if (null !== $otherParam['default']) {
                    continue;
                } ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?>],
        ];
        yield [
            'scenario' => '<?php echo $param['name']; ?> as an optional parameter (set to <?php echo 'int' === $param['type'] ? (int) $param['default'] + 1 : 'another_value'; ?>)',
            'body' => [<?php foreach ($scenario_parameters as $otherParam) {
                if (null !== $otherParam['default']) {
                    continue;
                } ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?>'<?php echo $param['name']; ?>' => <?php echo 'int' === $param['type'] ? (int) $param['default'] + 1 : "'another_value'"; ?>],
        ];
<?php } ?>
    }

<?php } ?>
    /**
     * @param array<string, int|string> $body
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails on $scenario')]
    public function test_it_fails_on_invalid_input(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/scenarios/<?php echo $scenario_kebab; ?>',
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
     *     body: array<string, int|string>,
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
<?php foreach ($scenario_parameters as $param) { ?>
        yield [
            'scenario' => 'invalid <?php echo $param['name']; ?>',
            'body' => [<?php foreach ($scenario_parameters as $otherParam) { ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']) { ?><?php echo 'int' === $otherParam['type'] ? '-1' : "'x'"; ?><?php } elseif ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?>],
        ];
<?php } ?>
    }
<?php } ?>
}
