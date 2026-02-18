<?php

declare(strict_types=1);

namespace Bl\ExceptionBundle;

use Bl\ExceptionBundle\EventListener\AppExceptionListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class BlExceptionBundle extends AbstractBundle
{
    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set(AppExceptionListener::class)
            ->autowire()
            ->autoconfigure()
        ;
    }
}
