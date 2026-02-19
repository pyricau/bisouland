<?php

declare(strict_types=1);

namespace Bl\AuthBundle;

use Bl\Auth\DeleteAuthToken;
use Bl\Auth\PdoPg\PdoPgDeleteAuthToken;
use Bl\Auth\PdoPg\PdoPgSaveAuthToken;
use Bl\Auth\SaveAuthToken;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class BlAuthBundle extends AbstractBundle
{
    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set(PdoPgSaveAuthToken::class)->autowire()->autoconfigure()
            ->alias(SaveAuthToken::class, PdoPgSaveAuthToken::class)

            ->set(PdoPgDeleteAuthToken::class)->autowire()->autoconfigure()
            ->alias(DeleteAuthToken::class, PdoPgDeleteAuthToken::class)
        ;
    }
}
