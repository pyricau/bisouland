<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Application\Action\<?= $action_name; ?>;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class <?= $class_name; ?>

{
    public function __construct(
        private readonly <?= $action_name; ?> $<?= $action_camel; ?>,
    ) {
    }

    #[Route('/api/v1/actions/<?= $action_kebab; ?>', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $this-><?= $action_camel; ?>->run(
<?php foreach ($action_parameters as $param): ?>
            $payload->getString('<?= $param['name']; ?>'),
<?php endforeach; ?>
        );

        // TODO: return appropriate response body
        return new JsonResponse(
            json_encode([], \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
