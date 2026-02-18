<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class HomepageController
{
    #[Route('/', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/actions/sign-up-new-player');
    }
}
