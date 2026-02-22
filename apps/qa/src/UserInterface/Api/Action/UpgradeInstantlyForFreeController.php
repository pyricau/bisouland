<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Action;

use Bl\Qa\Application\Action\UpgradeInstantlyForFree\UpgradeInstantlyForFree;
use Bl\Qa\Application\Action\UpgradeInstantlyForFree\UpgradeInstantlyForFreeHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class UpgradeInstantlyForFreeController
{
    public function __construct(
        private UpgradeInstantlyForFreeHandler $upgradeInstantlyForFreeHandler,
    ) {
    }

    #[Route('/api/v1/actions/upgrade-instantly-for-free', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        UpgradeInstantlyForFree $upgradeInstantlyForFree,
    ): JsonResponse {
        $upgradeInstantlyForFreed = $this->upgradeInstantlyForFreeHandler->run($upgradeInstantlyForFree);

        return new JsonResponse(
            json_encode($upgradeInstantlyForFreed->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
