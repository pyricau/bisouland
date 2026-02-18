<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class <?php echo $class_name; ?> extends TestCase
{
    public function test_it_renders_the_<?php echo $action_snake; ?>_page(): void
    {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/actions/<?php echo $action_kebab; ?>',
            method: 'GET',
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
    }
}
