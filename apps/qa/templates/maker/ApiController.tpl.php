<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>;
use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class <?php echo $class_name; ?>

{
    public function __construct(
        private <?php echo $action_name; ?>Handler $<?php echo $action_camel; ?>Handler,
    ) {
    }

    #[Route('/api/v1/actions/<?php echo $action_kebab; ?>', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        <?php echo $action_name; ?> $<?php echo $action_camel; ?>,
    ): JsonResponse {
        $output = $this-><?php echo $action_camel; ?>Handler->run($<?php echo $action_camel; ?>);

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
