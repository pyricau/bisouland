<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Scenario;

use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayer;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayerHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SignInNewPlayerController
{
    public function __construct(
        private SignInNewPlayerHandler $signInNewPlayerHandler,
    ) {
    }

    #[Route('/api/v1/scenarios/sign-in-new-player', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        SignInNewPlayer $signInNewPlayer,
    ): JsonResponse {
        $output = $this->signInNewPlayerHandler->run($signInNewPlayer);

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
