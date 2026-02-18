<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Web\Action;

use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class InstantFreeUpgradeController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/actions/instant-free-upgrade', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('actions/instant-free-upgrade.html.twig', [
            'upgradables' => Upgradable::cases(),
        ]));
    }
}
