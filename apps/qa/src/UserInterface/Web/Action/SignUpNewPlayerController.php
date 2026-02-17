<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class SignUpNewPlayerController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/actions/sign-up-new-player', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('actions/sign-up-new-player.html.twig'));
    }
}
