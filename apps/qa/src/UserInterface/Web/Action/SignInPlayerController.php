<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class SignInPlayerController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/actions/sign-in-player', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('qalin/action/sign-in-player.html.twig'));
    }
}
