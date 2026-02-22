<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Scenario\<?php echo $scenario_name; ?>\<?php echo $scenario_name; ?>;
use Bl\Qa\Application\Scenario\<?php echo $scenario_name; ?>\<?php echo $scenario_name; ?>Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class <?php echo $class_name; ?>

{
    public function __construct(
        private <?php echo $scenario_name; ?>Handler $<?php echo $scenario_camel; ?>Handler,
    ) {
    }

    #[Route('/api/v1/scenarios/<?php echo $scenario_kebab; ?>', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        <?php echo $scenario_name; ?> $<?php echo $scenario_camel; ?>,
    ): JsonResponse {
        $output = $this-><?php echo $scenario_camel; ?>Handler->run($<?php echo $scenario_camel; ?>);

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
