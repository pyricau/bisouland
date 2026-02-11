<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignUpNewPlayerController extends AbstractController
{
    #[Route('/actions/sign-up-new-player', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('actions/sign-up-new-player.html.twig');
    }
}
