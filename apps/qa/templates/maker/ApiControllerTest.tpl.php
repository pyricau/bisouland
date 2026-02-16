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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class <?php echo $class_name; ?> extends TestCase
{
    public function test_it_runs_action_successfully(): void
    {
<?php if ($has_username_param) { ?>
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
<?php } ?>
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/<?php echo $action_kebab; ?>',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
<?php if ('username' === $param['name']) { ?>
                '<?php echo $param['name']; ?>' => $username,
<?php } else { ?>
                '<?php echo $param['name']; ?>' => <?php echo $param['fixture_class']; ?>::makeString(),
<?php } ?>
<?php } else { ?>
                '<?php echo $param['name']; ?>' => 'valid_<?php echo $param['name']; ?>', // TODO: use fixture
<?php } ?>
<?php } ?>
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());
    }

<?php if (count($action_parameters) > 0) { ?>
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
            uri: '/api/v1/actions/<?php echo $action_kebab; ?>',
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
<?php foreach ($action_parameters as $i => $param) { ?>
        yield [
            'scenario' => '<?php echo $param['name']; ?> as a required parameter',
            'body' => [<?php foreach ($action_parameters as $j => $otherParam) { ?><?php if ($j !== $i) { ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::makeString()<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?><?php } ?>],
        ];
<?php } ?>
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
            uri: '/api/v1/actions/<?php echo $action_kebab; ?>',
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
<?php foreach ($action_parameters as $param) { ?>
        yield [
            'scenario' => 'invalid <?php echo $param['name']; ?>',
            'body' => [<?php foreach ($action_parameters as $otherParam) { ?>'<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['name'] === $param['name']) { ?>'x'<?php } elseif ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::makeString()<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?>, <?php } ?>],
        ];
<?php } ?>
    }
<?php } ?>
}
