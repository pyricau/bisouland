<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Scenario;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class SignInNewPlayerController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/scenarios/sign-in-new-player', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('scenarios/sign-in-new-player.html.twig'));
    }
}
