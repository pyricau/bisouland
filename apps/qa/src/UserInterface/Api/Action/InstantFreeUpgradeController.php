<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Action;

use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgrade;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class InstantFreeUpgradeController
{
    public function __construct(
        private InstantFreeUpgradeHandler $instantFreeUpgradeHandler,
    ) {
    }

    #[Route('/api/v1/actions/instant-free-upgrade', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $output = $this->instantFreeUpgradeHandler->run(new InstantFreeUpgrade(
            $payload->getString('username'),
            $payload->getString('upgradable'),
            $payload->getInt('levels'),
        ));

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
