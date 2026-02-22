<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Action;

use Bl\Game\Player\UpgradableLevels\Upgradable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class UpgradeInstantlyForFreeController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/actions/upgrade-instantly-for-free', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('qalin/action/upgrade-instantly-for-free.html.twig', [
            'upgradables' => Upgradable::cases(),
        ]));
    }
}
