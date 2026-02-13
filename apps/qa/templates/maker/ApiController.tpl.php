<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class <?php echo $class_name; ?>

{
    public function __construct(
        private readonly <?php echo $action_name; ?> $<?php echo $action_camel; ?>,
    ) {
    }

    #[Route('/api/v1/actions/<?php echo $action_kebab; ?>', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $this-><?php echo $action_camel; ?>->run(
<?php foreach ($action_parameters as $param) { ?>
            $payload->getString('<?php echo $param['name']; ?>'),
<?php } ?>
        );

        // TODO: return appropriate response body
        return new JsonResponse(
            json_encode([], \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
