<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Action;

use Bl\Qa\Application\Action\SignUpNewPlayer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignUpNewPlayerController
{
    public function __construct(
        private readonly SignUpNewPlayer $signUpNewPlayer,
    ) {
    }

    #[Route('/api/v1/actions/sign-up-new-player', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getPayload();

        $player = $this->signUpNewPlayer->run(
            $payload->getString('username'),
            $payload->getString('password'),
        );

        return new JsonResponse(
            json_encode([
                'account_id' => $player->account->accountId->toString(),
                'username' => $player->account->username->toString(),
                'love_points' => $player->lovePoints->toInt(),
                'score' => $player->score->toInt(),
                'cloud_coordinates_x' => $player->cloudCoordinates->getX(),
                'cloud_coordinates_y' => $player->cloudCoordinates->getY(),
            ], \JSON_THROW_ON_ERROR),
            Response::HTTP_CREATED,
            json: true,
        );
    }
}
