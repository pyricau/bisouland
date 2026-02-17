<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Action;

use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SignUpNewPlayerController
{
    public function __construct(
        private SignUpNewPlayerHandler $signUpNewPlayerHandler,
    ) {
    }

    #[Route('/api/v1/actions/sign-up-new-player', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $output = $this->signUpNewPlayerHandler->run(new SignUpNewPlayer(
            $payload->getString('username'),
            $payload->getString('password'),
        ));

        return new JsonResponse(
            json_encode($output->toArray(), \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
