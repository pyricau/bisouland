<?php

declare(strict_types=1);

namespace Bl\GameBundle;

use Bl\Game\ApplyCompletedUpgrade;
use Bl\Game\FindPlayer;
use Bl\Game\PdoPg\PdoPgApplyCompletedUpgrade;
use Bl\Game\PdoPg\PdoPgFindPlayer;
use Bl\Game\PdoPg\PdoPgSaveNewPlayer;
use Bl\Game\PdoPg\PdoPgSearchUsernames;
use Bl\Game\SaveNewPlayer;
use Bl\Game\SearchUsernames;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class BlGameBundle extends AbstractBundle
{
    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set(PdoPgSaveNewPlayer::class)->autowire()->autoconfigure()
            ->alias(SaveNewPlayer::class, PdoPgSaveNewPlayer::class)

            ->set(PdoPgFindPlayer::class)->autowire()->autoconfigure()
            ->alias(FindPlayer::class, PdoPgFindPlayer::class)

            ->set(PdoPgApplyCompletedUpgrade::class)->autowire()->autoconfigure()
            ->alias(ApplyCompletedUpgrade::class, PdoPgApplyCompletedUpgrade::class)

            ->set(PdoPgSearchUsernames::class)->autowire()->autoconfigure()
            ->alias(SearchUsernames::class, PdoPgSearchUsernames::class)
        ;
    }
}
