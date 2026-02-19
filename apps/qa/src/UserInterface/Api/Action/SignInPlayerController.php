<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Action;

use Bl\Qa\Application\Action\SignInPlayer\SignInPlayer;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SignInPlayerController
{
    public function __construct(
        private SignInPlayerHandler $signInPlayerHandler,
    ) {
    }

    #[Route('/api/v1/actions/sign-in-player', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload]
        SignInPlayer $signInPlayer,
    ): JsonResponse {
        $output = $this->signInPlayerHandler->run($signInPlayer);

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
